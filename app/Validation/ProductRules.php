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
        'stock' => [
            'rules' => 'required|integer|greater_than_equal_to[0]',
            'errors' => [
                'required' => 'Stok produk dibutuhkan.',
                'integer' => 'Stok produk harus berupa angka bulat.',
                'greater_than_equal_to' => 'Stok produk tidak boleh kurang dari 0.',
            ],
        ],
        'barcode' => [
            'rules' => 'permit_empty|is_unique[products.barcode]',
            'errors' => [
                'is_unique' => 'Barcode sudah terdaftar.',
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
        'stock' => [
            'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
            'errors' => [
                'integer' => 'Stok produk harus berupa angka bulat.',
                'greater_than_equal_to' => 'Stok produk tidak boleh kurang dari 0.',
            ],
        ],
        'barcode' => [
            'rules' => 'permit_empty',
            'errors' => [],
        ],
    ];
}
