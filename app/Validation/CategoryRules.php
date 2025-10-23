<?php

namespace App\Validation;

class CategoryRules
{
    public $create = [
        'category_name' => [
            'rules' => 'required|min_length[3]|is_unique[categories.category_name]',
            'errors' => [
                'required' => 'Nama kategori dibutuhkan.',
                'min_length' => 'Nama kategori harus terdiri dari minimal 3 karakter.',
                'max_length' => 'Nama kategori harus terdiri dari maksimal 50 karakter.',
                'is_unique' => 'Nama kategori sudah terdaftar.',
            ],
        ],
    ];

    public $update = [
        'category_name' => [
            'rules' => 'permit_empty|min_length[3]',
            'errors' => [
                'min_length' => 'Nama kategori harus terdiri dari minimal 3 karakter.',
            ],
        ],
    ];
}
