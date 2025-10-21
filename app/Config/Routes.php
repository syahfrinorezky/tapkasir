<?php

use App\Controllers\AdminController;
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

// admin
$routes->group('admin', ['filter' => 'role:admin'], function (RouteCollection $routes) {
    $routes->get('beranda', [AdminController::class, 'index'], ['as' => 'admin.dashboard']);
});