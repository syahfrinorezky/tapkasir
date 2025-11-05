<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RestockRequestModel;
use App\Models\ProductModel;
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
        $note = trim((string) ($data['note'] ?? '')) ?: null;

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

        return $this->response->setJSON(['restocks' => $list]);
    }

    public function adminApprove($id)
    {
        $session = session();
        $adminId = (int) $session->get('user_id');
        if (!$adminId) return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthenticated']);

        $model = new RestockRequestModel();
        $productModel = new ProductModel();
        $req = $model->find($id);
        if (!$req || $req['status'] !== 'pending') return $this->response->setStatusCode(404)->setJSON(['message' => 'Permintaan tidak ditemukan/sudah diproses']);

        $db = \Config\Database::connect();
        $db->transStart();
        try {
            $model->update($id, [
                'status' => 'approved',
                'approved_by' => $adminId,
                'decision_at' => date('Y-m-d H:i:s'),
            ]);

            $product = $productModel->find($req['product_id']);
            if ($product) {
                $newStock = (int)($product['stock'] ?? 0) + (int)($req['quantity'] ?? 0);
                $productModel->update($product['id'], ['stock' => $newStock]);
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
}

