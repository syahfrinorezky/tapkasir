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
            ->where('payment_status', 'paid')
            ->get()
            ->getRow()
            ->total ?? 0;

        $todayTransactions = $transactionModel
            ->where('DATE(transaction_date)', date('Y-m-d'))
            ->where('payment_status', 'paid')
            ->countAllResults();

        $transactionItemModel = new \App\Models\TransactionItemModel();
        $todayItemsSold = $transactionItemModel
            ->selectSum('transaction_items.quantity')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->where('DATE(transactions.transaction_date)', date('Y-m-d'))
            ->where('transactions.payment_status', 'paid')
            ->get()
            ->getRow()
            ->quantity ?? 0;

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
            ->where('payment_status', 'paid')
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

        $hourlySalesData = $transactionModel
            ->select('HOUR(transaction_date) as hour, SUM(total) as total')
            ->where('DATE(transaction_date)', date('Y-m-d'))
            ->where('payment_status', 'paid')
            ->groupBy('HOUR(transaction_date)')
            ->orderBy('HOUR(transaction_date)', 'ASC')
            ->findAll();

        $hourlyLabels = [];
        $hourlyTotals = [];

        for ($i = 0; $i < 24; $i++) {
            $hourlyLabels[] = sprintf('%02d:00', $i);
            $found = array_filter($hourlySalesData, fn($r) => (int)$r['hour'] === $i);
            $hourlyTotals[] = $found ? (float)array_values($found)[0]['total'] : 0;
        }

        $transactionItemModel = new \App\Models\TransactionItemModel();
        $topProducts = $transactionItemModel
            ->select('products.product_name as name, products.price, products.photo, categories.category_name, SUM(transaction_items.quantity) as total_sold')
            ->join('products', 'products.id = transaction_items.product_id')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->where('DATE(transactions.transaction_date)', date('Y-m-d'))
            ->where('transactions.payment_status', 'paid')
            ->groupBy('transaction_items.product_id')
            ->orderBy('total_sold', 'DESC')
            ->limit(6)
            ->findAll();

        foreach ($topProducts as &$p) {
            if ($p['photo']) {
                $p['photo'] = base_url($p['photo']);
            }
        }

        $recentTransactions = $transactionModel
            ->select('transactions.*, users.nama_lengkap as cashier_name')
            ->join('users', 'users.id = transactions.user_id')
            ->where('payment_status', 'paid')
            ->orderBy('transaction_date', 'DESC')
            ->limit(5)
            ->findAll();

        return $this->response->setJSON([
            'todaySales' => $todaySales,
            'todayTransactions' => $todayTransactions,
            'todayItemsSold' => $todayItemsSold,
            'productNeedRestock' => $productNeedRestock,
            'labels' => $labels,
            'totals' => $totals,
            'hourlyLabels' => $hourlyLabels,
            'hourlyTotals' => $hourlyTotals,
            'topProducts' => $topProducts,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
