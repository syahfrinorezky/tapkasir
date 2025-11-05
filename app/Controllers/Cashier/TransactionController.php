<?php

namespace App\Controllers\Cashier;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;
use App\Models\CashierWorkModel;
use App\Models\ShiftModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

class TransactionController extends BaseController
{
    public function index()
    {
        return view('app/cashier/transactions');
    }

    public function product($barcode = null)
    {
        $productModel = new ProductModel();

        $q = $this->request->getGet('q');
        if ($q !== null) {
            $products = $productModel
                ->like('barcode', $q)
                ->orLike('product_name', $q)
                ->where('stock >', 0)
                ->where('deleted_at', null)
                ->findAll(10);

            return $this->response->setJSON(['products' => $products]);
        }

        if (!$barcode) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Barcode tidak diberikan']);
        }

        $product = $productModel
            ->where('barcode', $barcode)
            ->where('deleted_at', null)
            ->first();

        if (!$product) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Produk tidak ditemukan']);
        }

        if (isset($product['stock']) && (int) $product['stock'] <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Stok produk kosong']);
        }

        return $this->response->setJSON(['product' => $product]);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['items']) || !is_array($data['items'])) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Items transaksi kosong']);
        }

        $userId = session('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'User tidak terautentikasi']);
        }

        $cashierWorkModel = new CashierWorkModel();
        $cashierWork = $cashierWorkModel->where('user_id', $userId)->where('status', 'active')->first();

        if (!$cashierWork) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada pekerjaan kasir aktif.']);
        }

        $transactionModel = new TransactionModel();
        $itemModel = new TransactionItemModel();
        $productModel = new ProductModel();

        $total = 0;
        foreach ($data['items'] as $it) {
            $p = $productModel->find($it['product_id']);
            if (!$p) {
                return $this->response->setStatusCode(404)->setJSON(['message' => 'Produk tidak ditemukan: ' . $it['product_id']]);
            }

            $qty = max(1, (int) $it['quantity']);

            if (isset($p['stock'])) {
                if ((int) $p['stock'] <= 0) {
                    return $this->response->setStatusCode(400)->setJSON(['message' => 'Stok produk kosong: ' . ($p['product_name'] ?? $p['barcode'] ?? $p['id'])]);
                }
                if ($qty > (int) $p['stock']) {
                    return $this->response->setStatusCode(400)->setJSON(['message' => 'Stok produk tidak mencukupi: ' . ($p['product_name'] ?? $p['barcode'] ?? $p['id']) . ' (stok: ' . (int) $p['stock'] . ')']);
                }
            }

            $subtotal = $p['price'] * $qty;
            $total += $subtotal;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $now = Time::now(config('App')->appTimezone ?? 'Asia/Makassar');

            $prefixDate = $now->format('Ymd');
            $prefix = 'TAP' . $prefixDate . '-';

            $last = $transactionModel
                ->select('no_transaction')
                ->where('DATE(transaction_date)', $now->toDateString())
                ->like('no_transaction', $prefix, 'after')
                ->orderBy('no_transaction', 'DESC')
                ->first();

            $seq = 1;
            if ($last && !empty($last['no_transaction'])) {
                if (preg_match('/-(\d{4,})$/', $last['no_transaction'], $m)) {
                    $seq = (int) $m[1] + 1;
                }
            } else {
                $countToday = $transactionModel
                    ->where('DATE(transaction_date)', $now->toDateString())
                    ->countAllResults();
                $seq = max(1, (int) $countToday + 1);
            }

            $noTransaction = $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

            $transactionId = $transactionModel->insert([
                'no_transaction' => $noTransaction,
                'user_id' => $userId,
                'cashier_work_id' => $cashierWork['id'],
                'transaction_date' => $now->toDateTimeString(),
                'total' => $total,
                'payment' => $data['payment'] ?? 0,
                'change' => max(0, ($data['payment'] ?? 0) - $total),
                'status' => 'completed',
                'created_at' => $now->toDateTimeString()
            ], true);

            foreach ($data['items'] as $it) {
                $p = $productModel->find($it['product_id']);
                $qty = (int) $it['quantity'];
                $subtotal = $p['price'] * $qty;

                $itemModel->insert([
                    'transaction_id' => $transactionId,
                    'product_id' => $p['id'],
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                    'created_at' => $now->toDateTimeString()
                ]);

                if (isset($p['stock'])) {
                    $newStock = max(0, $p['stock'] - $qty);
                    $productModel->update($p['id'], ['stock' => $newStock]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan transaksi');
            }

            return $this->response->setJSON(['message' => 'Transaksi berhasil', 'transaction_id' => $transactionId]);
        } catch (\Throwable $th) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['message' => $th->getMessage()]);
        }
    }

    public function receipt($id)
    {
        $transactionModel = new TransactionModel();
        $itemModel = new TransactionItemModel();
        $userModel = new UserModel();

        $transaction = $transactionModel->find($id);
        if (!$transaction) {
            return $this->response->setStatusCode(404)->setBody('Transaksi tidak ditemukan');
        }

        $items = $itemModel->where('transaction_id', $id)->findAll();
        $productModel = new ProductModel();
        $itemsWithProduct = [];
        foreach ($items as $it) {
            $p = $productModel->find($it['product_id']);
            $it['product_name'] = $p['product_name'] ?? ($p['barcode'] ?? 'Item');
            $itemsWithProduct[] = $it;
        }

        $user = $userModel->find($transaction['user_id']);

        return view('app/cashier/receipt', ['transaction' => $transaction, 'items' => $itemsWithProduct, 'user' => $user]);
    }

    public function shiftStatus()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthenticated']);
        }

        $role = strtolower((string) $session->get('role_name'));
        if ($role !== 'kasir') {
            return $this->response->setStatusCode(403)->setJSON(['message' => 'Forbidden']);
        }

        $userId = (int) $session->get('user_id');
        $tz = config('App')->appTimezone ?? 'Asia/Makassar';
        $now = Time::now($tz);

        $workModel = new CashierWorkModel();
        $shiftModel = new ShiftModel();

        $work = $workModel
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->orderBy('work_date', 'DESC')
            ->first();

        if (!$work) {
            return $this->response->setJSON([
                'active' => false,
                'message' => 'Tidak ada shift aktif',
                'now' => $now->toDateTimeString(),
            ]);
        }

        $shift = $shiftModel->find($work['shift_id'] ?? 0);
        if (!$shift) {
            return $this->response->setJSON([
                'active' => false,
                'message' => 'Shift tidak ditemukan',
                'now' => $now->toDateTimeString(),
            ]);
        }

        $workDate = $work['work_date'] ?? $now->toDateString();
        $startTime = $shift['start_time'] ?? '00:00:00';
        $endTime = $shift['end_time'] ?? '23:59:59';

        $start = Time::parse($workDate . ' ' . $startTime, $tz);
        $end = Time::parse($workDate . ' ' . $endTime, $tz);
        if ($end->isBefore($start)) {
            $end = $end->addDays(1);
        }

        $secondsLeft = max(0, $end->getTimestamp() - $now->getTimestamp());

        return $this->response->setJSON([
            'active' => true,
            'shift_name' => $shift['name'] ?? null,
            'work_date' => $workDate,
            'start_at' => $start->toDateTimeString(),
            'end_at' => $end->toDateTimeString(),
            'now' => $now->toDateTimeString(),
            'seconds_left' => $secondsLeft,
        ]);
    }

    public function log()
    {
        return view('app/cashier/transactions-log');
    }

    public function logData()
    {
        $transactionModel = new TransactionModel();

        $session = session();
        $userId = (int) $session->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthenticated']);
        }

        $date = $this->request->getGet('date') ?? date('Y-m-d');
        $shiftId = $this->request->getGet('shift_id');

        $builder = $transactionModel
            ->select('transactions.*, users.nama_lengkap as cashier, cashier_works.work_date as cashier_work_date, cashier_works.shift_id, shifts.name as shift_name')
            ->join('users', 'users.id = transactions.user_id', 'left')
            ->join('cashier_works', 'cashier_works.id = transactions.cashier_work_id', 'left')
            ->join('shifts', 'shifts.id = cashier_works.shift_id', 'left')
            ->where('transactions.deleted_at', null)
            ->where('transactions.user_id', $userId);

        if (!empty($date)) {
            $builder->where('DATE(transactions.transaction_date)', $date);
        }

        if (!empty($shiftId)) {
            $builder->where('cashier_works.shift_id', $shiftId);
        }

        $transactions = $builder->orderBy('transactions.transaction_date', 'DESC')->findAll();

        return $this->response->setJSON(['transactions' => $transactions, 'date' => $date, 'shift_id' => $shiftId]);
    }

    public function logItems($transactionId)
    {
        $session = session();
        $userId = (int) $session->get('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthenticated']);
        }

        $transactionModel = new TransactionModel();
        $itemModel = new TransactionItemModel();

        $tx = $transactionModel
            ->where('id', $transactionId)
            ->where('user_id', $userId)
            ->where('deleted_at', null)
            ->first();

        if (!$tx) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Transaksi tidak ditemukan']);
        }

        $items = $itemModel
            ->select('transaction_items.*, products.product_name, products.price as price')
            ->join('products', 'products.id = transaction_items.product_id', 'left')
            ->where('transaction_items.transaction_id', $transactionId)
            ->where('transaction_items.deleted_at', null)
            ->findAll();

        return $this->response->setJSON(['items' => $items]);
    }

    public function shiftsData()
    {
        $shiftModel = new ShiftModel();
        $shifts = $shiftModel
            ->select('id, name, start_time, end_time, status')
            ->orderBy('name', 'ASC')
            ->findAll();

        return $this->response->setJSON(['shifts' => $shifts]);
    }
}

