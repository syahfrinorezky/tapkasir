<?php

namespace App\Validation;

class ShiftRules
{
    public $create = [
        'name' => [
            'rules' => 'required|min_length[3]|is_unique[shifts.name]',
            'errors' => [
                'required' => 'Nama shift dibutuhkan.',
                'min_length' => 'Nama shift harus terdiri dari minimal 3 karakter.',
                'is_unique' => 'Nama shift sudah terdaftar.',
            ],
        ],
        'start_time' => [
            'rules' => 'required|valid_date[H:i:s]',
            'errors' => [
                'required' => 'Waktu mulai wajib diisi.',
                'valid_date' => 'Waktu mulai tidak valid (hh:mm:ss).',
            ],
        ],
        'end_time' => [
            'rules' => 'required|valid_date[H:i:s]',
            'errors' => [
                'required' => 'Waktu selesai wajib diisi.',
                'valid_date' => 'Waktu selesai tidak valid (hh:mm:ss).',
            ],
        ],
    ];

    public $update = [
        'name' => [
            'rules' => 'permit_empty|min_length[3]',
            'errors' => [
                'min_length' => 'Nama shift harus terdiri dari minimal 3 karakter.',
            ],
        ],
        'start_time' => [
            'rules' => 'permit_empty|valid_date[H:i:s]',
            'errors' => [
                'valid_date' => 'Waktu mulai tidak valid (hh:mm:ss).',
            ],
        ],
        'end_time' => [
            'rules' => 'permit_empty|valid_date[H:i:s]',
            'errors' => [
                'valid_date' => 'Waktu selesai tidak valid (hh:mm:ss).',
            ],
        ],
        'status' => [
            'rules' => 'permit_empty|in_list[active,inactive]',
            'errors' => [
                'in_list' => 'Status harus berupa active atau inactive.',
            ],
        ],
    ];
}
