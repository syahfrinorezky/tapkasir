<?php

namespace App\Controllers\Cashier;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;
use App\Models\CashierWorkModel;
use App\Models\ShiftModel;
use App\Models\UserModel;
use App\Models\ProductBatchModel;
use App\Libraries\StockService;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionController extends BaseController
{
    public function index()
    {
        $midtransConfig = config('Midtrans');
        $snapUrl = $midtransConfig->isProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';

        return view('app/cashier/transactions', [
            'midtransClientKey' => $midtransConfig->clientKey,
            'snapUrl' => $snapUrl
        ]);
    }

    public function product($barcode = null)
    {
        $productModel = new ProductModel();

        $q = $this->request->getGet('q');
        if ($q !== null) {
            $products = $productModel
                ->select('products.*, (SELECT COALESCE(SUM(current_stock), 0) FROM product_batches WHERE product_batches.product_id = products.id AND product_batches.deleted_at IS NULL AND (product_batches.expired_date > CURDATE() OR product_batches.expired_date IS NULL)) as real_stock')
                ->groupStart()
                ->like('barcode', $q)
                ->orLike('product_name', $q)
                ->groupEnd()
                ->having('real_stock >', 0)
                ->findAll(10);

            foreach ($products as &$p) {
                $p['stock'] = $p['real_stock'];
            }

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

        $batchModel = new ProductBatchModel();
        $sum = (int) ($batchModel
            ->select('COALESCE(SUM(current_stock),0) AS total')
            ->where('product_id', $product['id'])
            ->groupStart()
            ->where('expired_date >', date('Y-m-d'))
            ->orWhere('expired_date', null)
            ->groupEnd()
            ->where('deleted_at', null)
            ->first()['total'] ?? 0);

        $product['stock'] = $sum;

        if ($sum <= 0) {
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
        $batchModel = new ProductBatchModel();
        $db = \Config\Database::connect();

        $maxRetries = 3;
        $attempt = 0;
        $transactionId = null;
        $success = false;
        $errorMessage = 'Gagal menyimpan transaksi logic error';
        $snapToken = null;

        $paymentMethod = $data['payment_method'] ?? 'cash';
        $paymentStatus = ($paymentMethod === 'cash') ? 'paid' : 'pending';

        do {
            $attempt++;
            $db->transStart();

            try {
                $now = Time::now(config('App')->appTimezone ?? 'Asia/Makassar');

                $prefixDate = $now->format('Ymd');
                $prefix = 'TAP' . $prefixDate . '-';

                $last = $transactionModel
                    ->select('no_transaction')
                    ->where('DATE(transaction_date)', $now->toDateString())
                    ->like('no_transaction', $prefix, 'after')
                    ->orderBy('id', 'DESC')
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

                if ($attempt > 1) {
                    $seq += ($attempt - 1);
                }

                $noTransaction = $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

                $total = 0;
                $allocations = [];
                $itemsData = [];
                $midtransItems = [];

                foreach ($data['items'] as $it) {
                    if (empty($it['product_id'])) {
                        throw new \RuntimeException('Product ID tidak valid pada item transaksi.');
                    }

                    $p = $productModel->find($it['product_id']);
                    if (!$p) {
                        throw new \RuntimeException('Produk tidak ditemukan: ' . $it['product_id']);
                    }

                    $qty = max(1, (int) ($it['quantity'] ?? 1));
                    $sellingPrice = (float) $p['price'];

                    $alloc = StockService::allocateFromBatches((int) $p['id'], $qty);

                    $subtotal = $sellingPrice * $qty;
                    $total += $subtotal;

                    $itemsData[] = [
                        'product' => $p,
                        'qty' => $qty,
                        'price' => $sellingPrice,
                        'alloc' => $alloc,
                        'subtotal' => $subtotal,
                    ];

                    $midtransItems[] = [
                        'id' => $it['product_id'],
                        'price' => (int) $sellingPrice,
                        'quantity' => $qty,
                        'name' => substr($p['product_name'], 0, 50)
                    ];
                }

                $transactionId = $transactionModel->insert([
                    'no_transaction' => $noTransaction,
                    'user_id' => $userId,
                    'cashier_work_id' => $cashierWork['id'],
                    'transaction_date' => $now->toDateTimeString(),
                    'total' => $total,
                    'payment' => $data['payment'] ?? 0,
                    'change' => max(0, ($data['payment'] ?? 0) - $total),
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentStatus,
                    'created_at' => $now->toDateTimeString()
                ], true);

                foreach ($itemsData as $item) {
                    foreach ($item['alloc'] as $al) {
                        $batchId = (int) $al['batch_id'];
                        $takeQty = (int) $al['qty'];

                        $itemModel->insert([
                            'transaction_id' => $transactionId,
                            'product_id' => $item['product']['id'],
                            'batch_id' => $batchId,
                            'quantity' => $takeQty,
                            'subtotal' => $item['price'] * $takeQty,
                            'created_at' => $now->toDateTimeString()
                        ]);

                        $batchModel
                            ->where('id', $batchId)
                            ->where('current_stock >=', $takeQty)
                            ->decrement('current_stock', $takeQty);

                        if ($db->affectedRows() === 0) {
                            throw new \RuntimeException('Stok batch berubah/tidak cukup saat proses simpan. Silakan coba lagi.');
                        }
                    }
                }

                if ($paymentMethod === 'qris') {
                    $midtransConfig = config('Midtrans');
                    Config::$serverKey = $midtransConfig->serverKey;
                    Config::$isProduction = $midtransConfig->isProduction;
                    Config::$isSanitized = $midtransConfig->isSanitized;
                    Config::$is3ds = $midtransConfig->is3ds;

                    Config::$curlOptions = [
                        CURLOPT_HTTPHEADER => [],
                    ];

                    $params = [
                        'transaction_details' => [
                            'order_id' => $noTransaction . '-' . $transactionId,
                            'gross_amount' => (int) $total,
                        ],
                        'item_details' => $midtransItems,
                        'customer_details' => [
                            'first_name' => 'Customer',
                            'email' => 'customer@example.com',
                        ],
                    ];

                    $snapToken = Snap::getSnapToken($params);
                    $transactionModel->update($transactionId, ['snap_token' => $snapToken]);
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    $error = $db->error();
                    if (isset($error['code']) && $error['code'] == 1062) {
                        continue;
                    }
                    throw new \Exception('Gagal menyimpan transaksi: ' . ($error['message'] ?? 'Unknown error'));
                }

                $success = true;
                break;
            } catch (\Throwable $th) {
                $db->transRollback();
                $errorMessage = $th->getMessage();

                if (strpos($errorMessage, 'Duplicate entry') !== false) {
                    continue;
                }

                if (strpos($errorMessage, 'Stok') !== false) {
                    break;
                }
            }
        } while ($attempt < $maxRetries);

        if ($success) {
            return $this->response->setJSON([
                'message' => 'Transaksi berhasil',
                'transaction_id' => $transactionId,
                'no_transaction' => $noTransaction,
                'total' => $total,
                'snap_token' => $snapToken,
                'payment_method' => $paymentMethod
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON(['message' => $errorMessage]);
    }

    public function finishPayment()
    {
        $data = $this->request->getJSON(true);
        $transactionId = $data['transaction_id'] ?? null;
        $midtransId = $data['midtrans_id'] ?? null;

        if (!$transactionId) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Transaction ID required']);
        }

        $transactionModel = new TransactionModel();
        $transaction = $transactionModel->find($transactionId);

        if (!$transaction) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Transaction not found']);
        }

        $updateData = [
            'payment_status' => 'paid',
            'midtrans_id' => $midtransId,
            'transaction_date' => Time::now('Asia/Makassar')->toDateTimeString(),
        ];

        $transactionModel->update($transactionId, $updateData);

        return $this->response->setJSON(['message' => 'Payment finished']);
    }

    public function checkPending()
    {
        $userId = session('user_id');
        if (!$userId) {
            return $this->response->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        }

        $transactionModel = new TransactionModel();
        $pending = $transactionModel
            ->where('user_id', $userId)
            ->where('payment_method', 'qris')
            ->where('payment_status', 'pending')
            ->where('deleted_at', null)
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($pending) {
            return $this->response->setJSON([
                'has_pending' => true,
                'transaction_id' => $pending['id'],
                'snap_token' => $pending['snap_token'],
                'total' => $pending['total'],
                'no_transaction' => $pending['no_transaction'],
            ]);
        }

        return $this->response->setJSON(['has_pending' => false]);
    }

    public function cancel()
    {
        $data = $this->request->getJSON(true);
        $transactionId = $data['transaction_id'] ?? null;

        if (!$transactionId) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Transaction ID required']);
        }

        $userId = session('user_id');
        $transactionModel = new TransactionModel();
        $itemModel = new TransactionItemModel();
        $batchModel = new ProductBatchModel();

        $tx = $transactionModel->where('id', $transactionId)->where('user_id', $userId)->first();
        if (!$tx) {
            return $this->response->setJSON(['message' => 'Transaksi sudah dibatalkan atau tidak ditemukan']);
        }

        if ($tx['payment_status'] === 'paid') {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Transaksi sudah selesai, tidak bisa dibatalkan']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $items = $itemModel->where('transaction_id', $transactionId)->findAll();
            foreach ($items as $item) {
                if (!empty($item['batch_id'])) {
                    $batchModel->where('id', $item['batch_id'])
                        ->increment('current_stock', (int) $item['quantity']);
                }
            }

            $transactionModel->update($transactionId, [
                'payment_status' => 'cancelled',
                'deleted_at' => date('Y-m-d H:i:s')
            ]);

            $itemModel->where('transaction_id', $transactionId)->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal membatalkan transaksi');
            }

            return $this->response->setJSON(['message' => 'Transaksi dibatalkan']);
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

        $items = $itemModel
            ->select('transaction_items.*, products.product_name, products.price as selling_price, product_batches.purchase_price as purchase_price')
            ->join('products', 'products.id = transaction_items.product_id', 'left')
            ->join('product_batches', 'product_batches.id = transaction_items.batch_id', 'left')
            ->where('transaction_items.transaction_id', $id)
            ->where('transaction_items.deleted_at', null)
            ->findAll();

        $itemsWithProduct = [];
        foreach ($items as $it) {
            $sp = (float) ($it['selling_price'] ?? 0);
            $pp = (float) ($it['purchase_price'] ?? 0);
            $qty = (int) ($it['quantity'] ?? 0);
            $it['profit'] = ($sp - $pp) * $qty;
            $it['margin'] = $sp - $pp;
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
            ->where('transactions.payment_status', 'paid')
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
            ->select('transaction_items.*, products.product_name, product_batches.purchase_price as purchase_price')
            ->join('products', 'products.id = transaction_items.product_id', 'left')
            ->join('product_batches', 'product_batches.id = transaction_items.batch_id', 'left')
            ->where('transaction_items.transaction_id', $transactionId)
            ->where('transaction_items.deleted_at', null)
            ->findAll();

        foreach ($items as &$it) {
            $qty = (int) ($it['quantity'] ?? 0);
            $subtotal = (float) ($it['subtotal'] ?? 0);
            $sp = $qty > 0 ? $subtotal / $qty : 0;
            $pp = (float) ($it['purchase_price'] ?? 0);

            $it['selling_price'] = $sp;
            $it['profit'] = $subtotal - ($pp * $qty);
            $it['margin'] = $sp - $pp;
        }

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
