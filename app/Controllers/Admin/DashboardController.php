<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('app/admin/dashboard');
    }

    public function getSummaryData()
    {
        $userModel = new \App\Models\UserModel();
        $productModel = new \App\Models\ProductModel();
        $transactionModel = new \App\Models\TransactionModel();
        $cashierWorkModel = new \App\Models\CashierWorkModel();

        $todaySales = $transactionModel
            ->selectSum('total')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->get()
            ->getRow()
            ->total ?? 0;

        $pendingUser = $userModel
            ->where('status', 'pending')
            ->countAllResults();

        $activeCashiers = $cashierWorkModel
            ->where('work_date', date('Y-m-d'))
            ->countAllResults();

        $productNeedRestock = $productModel
            ->where('stock <', 10)
            ->countAllResults();

        $data = [
            'todaySales' => $todaySales,
            'pendingUser' => $pendingUser,
            'activeCashiers' => $activeCashiers,
            'productNeedRestock' => $productNeedRestock,
        ];

        return view('app/admin/dashboard', $data);
    }
}
