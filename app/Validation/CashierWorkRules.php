<?php

namespace App\Validation;

class CashierWorkRules
{
    public $create =
    [
        'user_id' => [
            'rules' => 'required|is_not_unique[users.id]',
            'errors' => [
                'required' => 'Kasir wajib dipilih.',
                'is_not_unique' => 'Kasir tidak valid.',
            ],
        ],
        'shift_id' => [
            'rules' => 'required|is_not_unique[shifts.id]',
            'errors' => [
                'required' => 'Shift wajib dipilih.',
                'is_not_unique' => 'Shift tidak valid.',
            ],
        ],
        'work_date' => [
            'rules' => 'required|valid_date',
            'errors' => [
                'required' => 'Tanggal kerja wajib diisi.',
                'valid_date' => 'Tanggal kerja tidak valid (yyyy-mm-dd).',
            ],
        ],
    ];

    public $update =
    [
        'status' => [
            'rules' => 'permit_empty|in_list[active,inactive]',
            'errors' => [
                'in_list' => 'Status harus berupa active atau inactive.',
            ],
        ],
    ];
}
