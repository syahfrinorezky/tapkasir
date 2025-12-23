<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\TransactionModel;
use App\Models\CashierWorkModel;

class DashboardController extends BaseController
{
    public function index()
    {
        return view('app/admin/dashboard');
    }

    public function data()
    {
        $userModel = new UserModel();
        $productModel = new ProductModel();
        $transactionModel = new TransactionModel();
        $cashierWorkModel = new CashierWorkModel();

        $todaySales = $transactionModel
            ->selectSum('total')
            ->where('DATE(transaction_date)', date('Y-m-d'))
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
            ->select('products.id')
            ->join('product_batches', 'product_batches.product_id = products.id AND product_batches.deleted_at IS NULL', 'left')
            ->groupBy('products.id')
            ->having('SUM(COALESCE(product_batches.current_stock, 0)) <', 10, false)
            ->get()
            ->getNumRows();

        $salesData = $transactionModel
            ->select('DATE(transaction_date) as date, SUM(total) as total')
            ->where('transaction_date >=', date('Y-m-d', strtotime('-6 days')))
            ->groupBy('DATE(transaction_date)')
            ->orderBy('DATE(transaction_date)', 'ASC')
            ->findAll();

        $period = new \DatePeriod(
            new \DateTime('-6 days'),
            new \DateInterval('P1D'),
            new \DateTime('+1 day')
        );

        $labels = [];
        $totals = [];

        foreach ($period as $date) {
            $dateFormat = $date->format('Y-m-d');

            $labels[] = $date->format('d M');

            $found = array_filter($salesData, fn($r) => $r['date'] === $dateFormat);
            $totals[] = $found ? array_values($found)[0]['total'] : 0;
        }

        $morningShiftData = $transactionModel
            ->select('HOUR(transaction_date) as hour, SUM(total) as total')
            ->where('transaction_date >=', date('Y-m-d') . ' 06:00:00')
            ->where('transaction_date <=', date('Y-m-d') . ' 17:59:59')
            ->groupBy('HOUR(transaction_date)')
            ->orderBy('HOUR(transaction_date)', 'ASC')
            ->findAll();

        $nightShiftData = $transactionModel
            ->select('HOUR(transaction_date) as hour, SUM(total) as total')
            ->groupStart()
            ->where('transaction_date >=', date('Y-m-d', strtotime('-1 day')) . ' 18:00:00')
            ->where('transaction_date <=', date('Y-m-d', strtotime('-1 day')) . ' 23:59:59')
            ->groupEnd()
            ->orGroupStart()
            ->where('transaction_date >=', date('Y-m-d') . ' 00:00:00')
            ->where('transaction_date <=', date('Y-m-d') . ' 05:59:59')
            ->groupEnd()
            ->groupBy('HOUR(transaction_date)')
            ->orderBy('HOUR(transaction_date)', 'ASC')
            ->findAll();


        $morningHours = array_map(fn($r) => sprintf('%02d:00', $r['hour']), $morningShiftData);
        $morningTotals = array_map(fn($r) => (float)$r['total'], $morningShiftData);
        $nightHours = array_map(fn($r) => sprintf('%02d:00', $r['hour']), $nightShiftData);
        $nightTotals = array_map(fn($r) => (float)$r['total'], $nightShiftData);

        return $this->response->setJSON([
            'todaySales' => $todaySales,
            'pendingUser' => $pendingUser,
            'activeCashiers' => $activeCashiers,
            'productNeedRestock' => $productNeedRestock,
            'labels' => $labels,
            'totals' => $totals,
            'morningHours' => $morningHours,
            'morningTotals' => $morningTotals,
            'nightHours' => $nightHours,
            'nightTotals' => $nightTotals,
        ]);
    }
}
