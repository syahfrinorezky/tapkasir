<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function index()
    {
        $userModel = new \App\Models\UserModel();
        $productModel = new \App\Models\ProductModel();
        $transactionModel = new \App\Models\TransactionModel();
        $cashierWorkModel = new \App\Models\CashierWorkModel();

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
            ->where('stock <', 10)
            ->countAllResults();

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
            ->where('transaction_date <=', date('Y-m-d') . ' 23:59:59')
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

        $data = [
            'todaySales' => $todaySales,
            'pendingUser' => $pendingUser,
            'activeCashiers' => $activeCashiers,
            'productNeedRestock' => $productNeedRestock,
            'labels' => json_encode($labels),
            'totals' => json_encode($totals),
            'morningHours' => json_encode($morningHours),
            'morningTotals' => json_encode($morningTotals),
            'nightHours' => json_encode($nightHours),
            'nightTotals' => json_encode($nightTotals),
        ];

        return view('app/admin/dashboard', $data);
    }
}
