<?php

use App\Controllers\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\RoleController;
use App\Controllers\Admin\ShiftController;
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\ProductController;
use App\Controllers\Admin\TransactionController;
use App\Controllers\ProductBatchController;
use App\Controllers\RestockRequestController;
use App\Controllers\Cashier\TransactionController as CashierTransactionController;
use App\Controllers\Cashier\ProductController as CashierProductController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// authentication routes
// register
$routes->get('daftar', [AuthController::class, 'register']);
$routes->post('daftar', [AuthController::class, 'register'], ['as' => 'register']);

// login
$routes->get('/', [AuthController::class, 'login']);
$routes->post('masuk', [AuthController::class, 'login'], ['as' => 'login']);

// logout
$routes->get('logout', [AuthController::class, 'logout'], ['as' => 'logout']);

// admin
$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
    $routes->get('dashboard', [DashboardController::class, 'index'], ['as' => 'admin.dashboard']);
    $routes->get('dashboard/data', [DashboardController::class, 'data'], ['as' => 'admin.dashboard.data']);

    // User management
    $routes->get('users', [UserController::class, 'index'], ['as' => 'admin.user']);
    $routes->post('users/updateStatus/(:num)', [UserController::class, 'updateStatus/$1'], ['as' => 'admin.user.updateStatus']);
    $routes->post('users/updateInfo/(:num)', [UserController::class, 'updateInfo/$1'], ['as' => 'admin.user.updateInfo']);
    $routes->delete('users/delete/(:num)', [UserController::class, 'delete/$1'], ['as' => 'admin.user.delete']);

    // Role management
    $routes->get('roles', [RoleController::class, 'index'], ['as' => 'admin.role']);
    $routes->post('roles/add', [RoleController::class, 'addRole'], ['as' => 'admin.role.add']);
    $routes->post('roles/edit/(:num)', [RoleController::class, 'editRole/$1'], ['as' => 'admin.role.edit']);
    $routes->delete('roles/delete/(:num)', [RoleController::class, 'deleteRole/$1'], ['as' => 'admin.role.delete']);

    // master-data
    // Product management
    $routes->get('products', [ProductController::class, 'index'], ['as' => 'admin.product']);
    $routes->get('products/data', [ProductController::class, 'data'], ['as' => 'admin.product.data']);
    $routes->get('products/batches/(:num)', [ProductBatchController::class, 'productBatches/$1']);
    $routes->get('products/barcode/image/(:segment)', [ProductController::class, 'barcodeImage/$1']);
    $routes->get('products/barcode/save/(:segment)', [ProductController::class, 'barcodeSave/$1']);
    $routes->post('products/add', [ProductController::class, 'add'], ['as' => 'admin.product.add']);
    $routes->post('products/edit/(:num)', [ProductController::class, 'edit/$1'], ['as' => 'admin.product.edit']);
    $routes->delete('products/delete/(:num)', [ProductController::class, 'delete/$1'], ['as' => 'admin.product.delete']);
    $routes->post('products/uploadPhoto/(:num)', [ProductController::class, 'uploadPhoto/$1'], ['as' => 'admin.product.uploadPhoto']);

    // Restock admin
    $routes->get('restocks/data', [RestockRequestController::class, 'adminList']);
    $routes->post('restocks/approve/(:num)', [RestockRequestController::class, 'adminApprove/$1']);
    $routes->post('restocks/reject/(:num)', [RestockRequestController::class, 'adminReject/$1']);

    // Category management
    $routes->post('products/addCategory', [ProductController::class, 'addCategory'], ['as' => 'admin.product.addCategory']);
    $routes->post('products/editCategory/(:num)', [ProductController::class, 'editCategory/$1'], ['as' => 'admin.product.editCategory']);
    $routes->delete('products/deleteCategory/(:num)', [ProductController::class, 'deleteCategory/$1'], ['as' => 'admin.product.deleteCategory']);

    // Storage Location management
    $routes->post('products/addLocation', [ProductController::class, 'addLocation'], ['as' => 'admin.product.addLocation']);
    $routes->post('products/editLocation/(:num)', [ProductController::class, 'editLocation/$1'], ['as' => 'admin.product.editLocation']);
    $routes->delete('products/deleteLocation/(:num)', [ProductController::class, 'deleteLocation/$1'], ['as' => 'admin.product.deleteLocation']);


    // Shift management
    $routes->get('shifts', [ShiftController::class, 'index'], ['as' => 'admin.shift']);
    $routes->get('shifts/data', [ShiftController::class, 'data']);
    $routes->post('shifts/add', [ShiftController::class, 'addShift']);
    $routes->post('shifts/edit/(:num)', [ShiftController::class, 'editShift/$1']);
    $routes->post('shifts/updateCashierShift/(:num)', [ShiftController::class, 'updateCashierShift/$1']);


    // Transactions
    $routes->get('transactions', [TransactionController::class, 'index'], ['as' => 'admin.transaction']);
    $routes->get('transactions/data', [TransactionController::class, 'data'], ['as' => 'admin.transaction.data']);
    $routes->get('transactions/items/(:num)', [TransactionController::class, 'items']);
});

// cashier
$routes->group('cashier', ['filter' => 'role:kasir'], function ($routes) {
    // Transactions
    $routes->get('transactions', [CashierTransactionController::class, 'index'], ['as' => 'cashier.transactions']);
    $routes->get('transactions/product', [CashierTransactionController::class, 'product']);
    $routes->get('transactions/product/(:segment)', [CashierTransactionController::class, 'product']);
    $routes->get('transactions/receipt/(:num)', [CashierTransactionController::class, 'receipt']);
    $routes->post('transactions/create', [CashierTransactionController::class, 'create']);
    $routes->post('transactions/finish', [CashierTransactionController::class, 'finishPayment']);
    // Shift status
    $routes->get('shift/status', [CashierTransactionController::class, 'shiftStatus']);

    // Cashier products
    $routes->get('products', [CashierProductController::class, 'index'], ['as' => 'cashier.products']);
    $routes->get('products/data', [ProductController::class, 'data']);
    // Product batches for UI modal
    $routes->get('products/batches/(:num)', [ProductBatchController::class, 'productBatches/$1']);

    // Barcode image accessible for cashier
    $routes->get('products/barcode/image/(:segment)', [ProductController::class, 'barcodeImage/$1']);

    // Restock
    $routes->get('restocks/my', [RestockRequestController::class, 'cashierList']);
    $routes->post('restocks', [RestockRequestController::class, 'cashierCreate']);
    $routes->post('restocks/uploadReceipt', [RestockRequestController::class, 'uploadReceipt']);

    // Cashier Transaction Logs (personal)
    $routes->get('transactions/log', [CashierTransactionController::class, 'log'], ['as' => 'cashier.transactions.log']);
    $routes->get('transactions/log/data', [CashierTransactionController::class, 'logData'], ['as' => 'cashier.transactions.log.data']);
    $routes->get('transactions/log/items/(:num)', [CashierTransactionController::class, 'logItems/$1'], ['as' => 'cashier.transactions.log.items']);
    // Shifts (for filter dropdown)
    $routes->get('shifts/data', [CashierTransactionController::class, 'shiftsData']);
});
