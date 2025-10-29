<?php

use App\Controllers\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\RoleController;
use App\Controllers\Admin\ShiftController;
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\ProductController;
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

    // master-data
    // Product management
    $routes->get('products', [ProductController::class, 'index'], ['as' => 'admin.product']);
    $routes->get('products/data', [ProductController::class, 'data'], ['as' => 'admin.product.data']);
    $routes->post('products/add', [ProductController::class, 'add'], ['as' => 'admin.product.add']);
    $routes->post('products/edit/(:num)', [ProductController::class, 'edit/$1'], ['as' => 'admin.product.edit']);
    $routes->delete('products/delete/(:num)', [ProductController::class, 'delete/$1'], ['as' => 'admin.product.delete']);
    $routes->post('products/uploadPhoto/(:num)', [ProductController::class, 'uploadPhoto/$1'], ['as' => 'admin.product.uploadPhoto']);

    // Category management
    $routes->post('products/addCategory', [ProductController::class, 'addCategory'], ['as' => 'admin.product.addCategory']);
    $routes->post('products/editCategory/(:num)', [ProductController::class, 'editCategory/$1'], ['as' => 'admin.product.editCategory']);
    $routes->delete('products/deleteCategory/(:num)', [ProductController::class, 'deleteCategory/$1'], ['as' => 'admin.product.deleteCategory']);


    // Shift management
    $routes->get('shifts', [ShiftController::class, 'index'], ['as' => 'admin.shift']);
    $routes->get('shifts/data', [ShiftController::class, 'data']);
    $routes->post('shifts/add', [ShiftController::class, 'addShift']);
    $routes->post('shifts/edit/(:num)', [ShiftController::class, 'editShift/$1']);
    $routes->post('shifts/updateCashierShift/(:num)', [ShiftController::class, 'updateCashierShift/$1']);
});
