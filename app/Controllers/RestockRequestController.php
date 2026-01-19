<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RestockRequestModel;
use App\Models\ProductModel;
use App\Models\ProductBatchModel;
use App\Libraries\StockService;
use CodeIgniter\I18n\Time;

class RestockRequestController extends BaseController
{

    public function cashierCreate()
    {
        $session = session();
        $userId = (int) $session->get('user_id');
        if (!$userId) return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthenticated']);

        $data = $this->request->getJSON(true);
        $productId = (int) ($data['product_id'] ?? 0);
        $qty = max(1, (int) ($data['quantity'] ?? 1));
        $noteText = trim((string) ($data['note'] ?? '')) ?: null;

        $rack = null;
        $row = null;
        if (!empty($data['location_id'])) {
            $locModel = new \App\Models\StorageLocationModel();
            $loc = $locModel->find($data['location_id']);
            if ($loc) {
                $rack = $loc['rack'];
                $row = $loc['row'];
            }
        }

        $details = [
            'expired_date'   => ($data['expired_date'] ?? null) ?: null,
            'purchase_price' => isset($data['purchase_price']) ? (float) $data['purchase_price'] : null,
            'location_id'    => $data['location_id'] ?? null,
            'rack'           => $rack,
            'row'            => $row,
            'receipt_temp'   => $data['receipt_temp'] ?? null,
            'note'           => $noteText,
        ];
        $note = json_encode($details);

        if ($productId <= 0) return $this->response->setStatusCode(400)->setJSON(['message' => 'Produk tidak valid']);
        $productModel = new ProductModel();
        $product = $productModel->find($productId);
        if (!$product || !empty($product['deleted_at'])) return $this->response->setStatusCode(404)->setJSON(['message' => 'Produk tidak ditemukan']);

        $model = new RestockRequestModel();
        $now = Time::now(config('App')->appTimezone ?? 'Asia/Makassar');
        $model->insert([
            'product_id' => $productId,
            'user_id' => $userId,
            'quantity' => $qty,
            'note' => $note,
            'status' => 'pending',
            'created_at' => $now->toDateTimeString(),
            'updated_at' => $now->toDateTimeString(),
        ]);

        return $this->response->setJSON(['message' => 'Permintaan restock dikirim']);
    }

    public function cashierList()
    {
        $session = session();
        $userId = (int) $session->get('user_id');
        if (!$userId) return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthenticated']);

        $model = new RestockRequestModel();
        $list = $model
            ->select('restock_requests.*, products.product_name, products.barcode')
            ->join('products', 'products.id = restock_requests.product_id', 'left')
            ->where('restock_requests.user_id', $userId)
            ->where('restock_requests.deleted_at', null)
            ->orderBy('restock_requests.created_at', 'DESC')
            ->findAll();

        foreach ($list as &$item) {
            $details = json_decode($item['note'] ?? '', true);
            $item['receipt_image'] = $details['receipt_temp'] ?? null;
            if ($item['receipt_image']) {
                $item['receipt_image'] = base_url($item['receipt_image']);
            }
            $item['user_note'] = $details['note'] ?? null;
        }

        return $this->response->setJSON(['restocks' => $list]);
    }

    public function adminList()
    {
        $model = new RestockRequestModel();
        $list = $model
            ->select('restock_requests.*, products.product_name, products.barcode, users.nama_lengkap as requester')
            ->join('products', 'products.id = restock_requests.product_id', 'left')
            ->join('users', 'users.id = restock_requests.user_id', 'left')
            ->where('restock_requests.deleted_at', null)
            ->orderBy('restock_requests.created_at', 'DESC')
            ->findAll();

        foreach ($list as &$item) {
            $details = json_decode($item['note'] ?? '', true);
            $item['receipt_image'] = $details['receipt_temp'] ?? null;
            if ($item['receipt_image']) {
                $item['receipt_image'] = base_url($item['receipt_image']);
            }
            $item['user_note'] = $details['note'] ?? null;
        }

        return $this->response->setJSON(['restocks' => $list]);
    }

    public function adminApprove($id)
    {
        $session = session();
        $adminId = (int) $session->get('user_id');
        if (!$adminId) return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthenticated']);

        $model = new RestockRequestModel();
        $productModel = new ProductModel();
        $batchModel = new ProductBatchModel();
        $req = $model->find($id);
        if (!$req || $req['status'] !== 'pending') return $this->response->setStatusCode(404)->setJSON(['message' => 'Permintaan tidak ditemukan/sudah diproses']);

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $body = $this->request->getJSON(true) ?? [];

            $noteDetails = [];
            if (!empty($req['note'])) {
                try {
                    $decoded = json_decode((string) $req['note'], true);
                    if (is_array($decoded)) $noteDetails = $decoded;
                } catch (\Throwable $e) {
                }
            }

            $expired = isset($body['expired_date']) && $body['expired_date'] !== ''
                ? $body['expired_date']
                : ($noteDetails['expired_date'] ?? null);
            $purchase = isset($body['purchase_price'])
                ? (float) $body['purchase_price']
                : (float) ($noteDetails['purchase_price'] ?? 0.0);
            $locationId = $body['location_id'] ?? ($noteDetails['location_id'] ?? null); 
            $receiptImage = $body['receipt_image'] ?? ($noteDetails['receipt_temp'] ?? null);

            $model->update($id, [
                'status' => 'approved',
                'approved_by' => $adminId,
                'decision_at' => date('Y-m-d H:i:s'),
            ]);

            $product = $productModel->find($req['product_id']);
            if ($product) {
                $qty = (int) ($req['quantity'] ?? 0);
                $batchCode = 'B' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid('', true)), 0, 6));

                if ($receiptImage && strpos($receiptImage, 'writable/') === 0) {
                    $newPath = $this->moveReceiptToPublic($receiptImage);
                    if ($newPath) $receiptImage = $newPath;
                }

                $batchModel->insert([
                    'product_id' => $product['id'],
                    'batch_code' => $batchCode,
                    'expired_date' => $expired,
                    'purchase_price' => $purchase,
                    'initial_stock' => $qty,
                    'current_stock' => $qty,
                    'location_id' => $locationId, 
                    'receipt_image' => $receiptImage,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $db->transComplete();
            if ($db->transStatus() === false) throw new \Exception('Gagal menyetujui restock');
            return $this->response->setJSON(['message' => 'Permintaan restock disetujui']);
        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['message' => $e->getMessage()]);
        }
    }

    public function adminReject($id)
    {
        $session = session();
        $adminId = (int) $session->get('user_id');
        if (!$adminId) return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthenticated']);

        $model = new RestockRequestModel();
        $req = $model->find($id);
        if (!$req || $req['status'] !== 'pending') return $this->response->setStatusCode(404)->setJSON(['message' => 'Permintaan tidak ditemukan/sudah diproses']);

        $ok = $model->update($id, [
            'status' => 'rejected',
            'approved_by' => $adminId,
            'decision_at' => date('Y-m-d H:i:s'),
        ]);

        if ($ok) return $this->response->setJSON(['message' => 'Permintaan restock ditolak']);
        return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menolak permintaan']);
    }
    public function uploadReceipt()
    {
        $session = session();
        $userId = (int) $session->get('user_id');
        if (!$userId) return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthenticated']);

        $file = $this->request->getFile('receipt');
        if (!$file || !$file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'File tidak valid']);
        }

        $ext = strtolower($file->getClientExtension());
        $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'heic', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tipe file tidak didukung']);
        }
        
        $mime = $file->getMimeType();
        $allowedMimes = [
            'image/jpeg', 'image/png', 'application/pdf', 'image/heic', 'image/webp'
        ];
        
        if (!in_array($mime, $allowedMimes)) {
             return $this->response->setStatusCode(400)->setJSON(['message' => 'Format file tidak valid (MIME type mismatch)']);
        }

        $publicTempDir = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'restock_temp';
        if (!is_dir($publicTempDir)) @mkdir($publicTempDir, 0775, true);

        $newName = 'tmp_' . $userId . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (!$file->move($publicTempDir, $newName)) {
            return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menyimpan file']);
        }

        $relPublicPath = 'uploads/restock_temp/' . $newName;
        return $this->response->setJSON(['path' => $relPublicPath, 'name' => $file->getClientName()]);
    }

    private function moveReceiptToPublic(string $tempPath): ?string
    {
        $root = rtrim(FCPATH, '/\\');
        
        $fullTemp = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $tempPath);
        
        if (!is_file($fullTemp)) return null;

        $targetDir = $root . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'restocks';
        if (!is_dir($targetDir)) @mkdir($targetDir, 0775, true);

        $ext = pathinfo($fullTemp, PATHINFO_EXTENSION) ?: 'jpg';
        $fileName = 'receipt_' . date('Ymd_His') . '_' . substr(md5($fullTemp . microtime(true)), 0, 6) . '.' . $ext;
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        if (@rename($fullTemp, $targetPath)) {
            return 'uploads/restocks/' . $fileName;
        }
        return null;
    }
}
