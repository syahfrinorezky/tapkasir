<?php

namespace App\Validation;

class TransactionRules
{
    public $create = [
        'user_id' => [
            'rules' => 'required|is_not_unique[users.id]',
            'errors' => [
                'required' => 'Kasir wajib diisi.',
                'is_not_unique' => 'Kasir tidak valid.',
            ],
        ],
        'cashier_work_id' => [
            'rules' => 'required|is_not_unique[cashier_works.id]',
            'errors' => [
                'required' => 'Sesi kerja kasir wajib diisi.',
                'is_not_unique' => 'Sesi kerja kasir tidak valid.',
            ],
        ],
        'total' => [
            'rules' => 'required|decimal|greater_than[0]',
            'errors' => [
                'required' => 'Total transaksi wajib diisi.',
                'decimal' => 'Total transaksi harus berupa angka desimal.',
                'greater_than' => 'Total transaksi harus lebih dari 0.',
            ],
        ],
        'payment' => [
            'rules' => 'required|decimal|greater_than[0]',
            'errors' => [
                'required' => 'Jumlah pembayaran wajib diisi.',
                'decimal' => 'Jumlah pembayaran harus berupa angka desimal.',
                'greater_than' => 'Jumlah pembayaran harus lebih dari 0.',
            ],
        ],
        'transaction_date' => [
            'rules' => 'required|valid_date',
            'errors' => [
                'required' => 'Tanggal transaksi wajib diisi.',
                'valid_date' => 'Tanggal transaksi tidak valid.',
            ],
        ],
        'items' => [
            'rules' => 'required',
            'errors' => [
                'required' => 'Item transaksi wajib diisi.',
            ],
        ],
    ];
}
