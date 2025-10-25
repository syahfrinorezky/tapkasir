<?php

use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\UserController;
use App\Controllers\AuthController;
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
$routes->group('admin', ['filter' => 'role:admin'], function (RouteCollection $routes) {
    $routes->get('dashboard', [DashboardController::class, 'index'], ['as' => 'admin.dashboard']);
    
    // user management
    $routes->get('users', [UserController::class, 'index'], ['as' => 'admin.user']);
});
