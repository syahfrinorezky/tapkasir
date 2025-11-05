<?php

namespace App\Controllers\Cashier;

use App\Controllers\BaseController;

class ProductController extends BaseController
{
    public function index()
    {
        return view('app/cashier/products');
    }
}
