<?php

namespace App\Validation;

class ProductRules
{
    public $create = [
        'product_name' => [
            'rules' => 'required|min_length[3]|max_length[100]',
            'errors' => [
                'required' => 'Nama produk dibutuhkan.',
                'min_length' => 'Nama produk harus terdiri dari minimal 3 karakter.',
                'max_length' => 'Nama produk harus terdiri dari maksimal 100 karakter.',
            ],
        ],
        'price' => [
            'rules' => 'required|decimal',
            'errors' => [
                'required' => 'Harga produk dibutuhkan.',
                'decimal' => 'Harga produk harus berupa angka desimal.',
            ],
        ],
        'category_id' => [
            'rules' => 'required|is_not_unique[categories.id]',
            'errors' => [
                'required' => 'Kategori produk dibutuhkan.',
                'is_not_unique' => 'Kategori produk tidak valid.',
            ],
        ],
    ];


    public $update = [
        'product_name' => [
            'rules' => 'permit_empty|min_length[3]|max_length[100]',
            'errors' => [
                'min_length' => 'Nama produk harus terdiri dari minimal 3 karakter.',
                'max_length' => 'Nama produk harus terdiri dari maksimal 100 karakter.',
            ],
        ],
        'price' => [
            'rules' => 'permit_empty|decimal',
            'errors' => [
                'decimal' => 'Harga produk harus berupa angka desimal.',
            ],
        ],
        'category_id' => [
            'rules' => 'permit_empty|is_not_unique[categories.id]',
            'errors' => [
                'is_not_unique' => 'Kategori produk tidak valid.',
            ],
        ],
    ];
}
