<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;

class TransactionController extends BaseController
{
    public function index()
    {
        if ($this->request->isAJAX()) {
            return $this->data();
        }
        return view('app/admin/transactions');
    }

    public function data()
    {
        $transactionModel = new TransactionModel();

        $date = $this->request->getGet('date') ?? date('Y-m-d');
        $shiftId = $this->request->getGet('shift_id');

        $builder = $transactionModel
            ->select('transactions.*, users.nama_lengkap as cashier, cashier_works.work_date as cashier_work_date, cashier_works.shift_id, shifts.name as shift_name')
            ->join('users', 'users.id = transactions.user_id', 'left')
            ->join('cashier_works', 'cashier_works.id = transactions.cashier_work_id', 'left')
            ->join('shifts', 'shifts.id = cashier_works.shift_id', 'left')
            ->where('transactions.deleted_at', null);

        if (!empty($date)) {
            $builder->where('DATE(transactions.transaction_date)', $date);
        }

        if (!empty($shiftId)) {
            $builder->where('cashier_works.shift_id', $shiftId);
        }

        $transactions = $builder->orderBy('transactions.transaction_date', 'DESC')->findAll();

        return $this->response->setJSON(['transactions' => $transactions, 'date' => $date, 'shift_id' => $shiftId]);
    }

    public function items($transactionId)
    {
        $itemModel = new TransactionItemModel();

        $items = $itemModel
            ->select('transaction_items.*, products.product_name, products.price as price')
            ->join('products', 'products.id = transaction_items.product_id', 'left')
            ->where('transaction_items.transaction_id', $transactionId)
            ->where('transaction_items.deleted_at', null)
            ->findAll();

        return $this->response->setJSON(['items' => $items]);
    }
}
