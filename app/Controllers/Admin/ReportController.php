<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransactionModel;
use App\Models\TransactionItemModel;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class ReportController extends BaseController
{
    public function index()
    {
        return view('app/admin/reports');
    }

    public function data()
    {
        $transactionModel = new TransactionModel();
        $transactionItemModel = new TransactionItemModel();

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        // 1. Summary Data (Revenue, Transactions)
        $summary = $transactionModel
            ->selectSum('total', 'total_sales')
            ->selectCount('id', 'total_transactions')
            ->where('DATE(transaction_date) >=', $startDate)
            ->where('DATE(transaction_date) <=', $endDate)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        // 2. Profit Calculation (Requires joining items and batches)
        // Profit = Sum(item.subtotal - (item.quantity * batch.purchase_price))
        $profitData = $transactionItemModel
            ->selectSum('transaction_items.subtotal', 'total_revenue')
            ->select('SUM(transaction_items.quantity * COALESCE(product_batches.purchase_price, 0)) as total_cogs')
            ->selectSum('transaction_items.quantity', 'total_items_sold')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->join('product_batches', 'product_batches.id = transaction_items.batch_id', 'left')
            ->where('DATE(transactions.transaction_date) >=', $startDate)
            ->where('DATE(transactions.transaction_date) <=', $endDate)
            ->where('transactions.deleted_at', null)
            ->where('transaction_items.deleted_at', null)
            ->get()
            ->getRowArray();

        $totalRevenue = (float) ($profitData['total_revenue'] ?? 0);
        $totalCogs = (float) ($profitData['total_cogs'] ?? 0);
        $totalProfit = $totalRevenue - $totalCogs;
        $totalItems = (int) ($profitData['total_items_sold'] ?? 0);

        // 3. Daily Sales & Profit Chart
        $dailyData = $transactionItemModel
            ->select('DATE(transactions.transaction_date) as date')
            ->selectSum('transaction_items.subtotal', 'revenue')
            ->select('SUM(transaction_items.quantity * COALESCE(product_batches.purchase_price, 0)) as cogs')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->join('product_batches', 'product_batches.id = transaction_items.batch_id', 'left')
            ->where('DATE(transactions.transaction_date) >=', $startDate)
            ->where('DATE(transactions.transaction_date) <=', $endDate)
            ->where('transactions.deleted_at', null)
            ->where('transaction_items.deleted_at', null)
            ->groupBy('DATE(transactions.transaction_date)')
            ->orderBy('date', 'ASC')
            ->findAll();

        $dailyChart = [];
        foreach ($dailyData as $day) {
            $rev = (float) $day['revenue'];
            $cogs = (float) $day['cogs'];
            $dailyChart[] = [
                'date' => $day['date'],
                'revenue' => $rev,
                'profit' => $rev - $cogs
            ];
        }

        // 4. Hourly Sales (Heatmap/Busy Hours)
        $hourlyData = $transactionModel
            ->select('HOUR(transaction_date) as hour, COUNT(id) as count, SUM(total) as total')
            ->where('DATE(transaction_date) >=', $startDate)
            ->where('DATE(transaction_date) <=', $endDate)
            ->where('deleted_at', null)
            ->groupBy('HOUR(transaction_date)')
            ->orderBy('hour', 'ASC')
            ->findAll();

        // 5. Category Performance
        $categoryData = $transactionItemModel
            ->select('categories.category_name, SUM(transaction_items.subtotal) as total_sales')
            ->join('products', 'products.id = transaction_items.product_id')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->where('DATE(transactions.transaction_date) >=', $startDate)
            ->where('DATE(transactions.transaction_date) <=', $endDate)
            ->where('transactions.deleted_at', null)
            ->where('transaction_items.deleted_at', null)
            ->groupBy('categories.id') // Group by ID to handle same names
            ->orderBy('total_sales', 'DESC')
            ->findAll();

        // 6. Top Products
        $productData = $transactionItemModel
            ->select('products.product_name, categories.category_name')
            ->selectSum('transaction_items.quantity', 'qty')
            ->selectSum('transaction_items.subtotal', 'revenue')
            ->select('SUM(transaction_items.quantity * COALESCE(product_batches.purchase_price, 0)) as cogs')
            ->join('products', 'products.id = transaction_items.product_id')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->join('product_batches', 'product_batches.id = transaction_items.batch_id', 'left')
            ->join('transactions', 'transactions.id = transaction_items.transaction_id')
            ->where('DATE(transactions.transaction_date) >=', $startDate)
            ->where('DATE(transactions.transaction_date) <=', $endDate)
            ->where('transactions.deleted_at', null)
            ->where('transaction_items.deleted_at', null)
            ->groupBy('products.id')
            ->orderBy('revenue', 'DESC')
            ->limit(10)
            ->findAll();

        $topProducts = [];
        foreach ($productData as $prod) {
            $rev = (float) $prod['revenue'];
            $cogs = (float) $prod['cogs'];
            $topProducts[] = [
                'product_name' => $prod['product_name'],
                'category_name' => $prod['category_name'] ?? 'Uncategorized',
                'qty' => (int) $prod['qty'],
                'revenue' => $rev,
                'profit' => $rev - $cogs
            ];
        }

        // 7. Cashier Performance
        $cashierData = $transactionModel
            ->select('users.nama_lengkap as cashier_name, COUNT(transactions.id) as total_transactions, SUM(transactions.total) as total_sales')
            ->join('users', 'users.id = transactions.user_id', 'left')
            ->where('DATE(transaction_date) >=', $startDate)
            ->where('DATE(transaction_date) <=', $endDate)
            ->where('transactions.deleted_at', null)
            ->groupBy('users.id')
            ->orderBy('total_sales', 'DESC')
            ->findAll();

        return $this->response->setJSON([
            'summary' => [
                'total_sales' => (float) ($summary['total_sales'] ?? 0),
                'total_profit' => $totalProfit,
                'total_transactions' => (int) ($summary['total_transactions'] ?? 0),
                'total_items' => $totalItems
            ],
            'charts' => [
                'daily' => $dailyChart,
                'hourly' => $hourlyData,
                'categories' => $categoryData
            ],
            'tables' => [
                'top_products' => $topProducts,
                'cashiers' => $cashierData
            ]
        ]);
    }
}
