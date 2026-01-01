<?php

namespace App\Validation;

class UserRules
{
    public $registration = [
        'nama_lengkap' => [
            'rules' => 'required|min_length[1]',
            'errors' => [
                'required' => 'Nama lengkap dibutuhkan',
                'min_length' => 'Nama lengkap harus terdiri dari minimal 1 karakter',
            ]
        ],
        'email' => [
            'rules' => 'required|valid_email|is_unique[users.email]',
            'errors' => [
                'required' => 'Email dibutuhkan',
                'valid_email' => 'Email tidak valid',
                'is_unique' => 'Email sudah terdaftar',
            ]
        ],
        'password' => [
            'rules' => 'required|min_length[8]',
            'errors' => [
                'required' => 'Password dibutuhkan',
                'min_length' => 'Password harus terdiri dari minimal 8 karakter',
            ]
        ]
    ];

    public $login = [
        'email' => [
            'rules' => 'required|valid_email',
            'errors' => [
                'required' => 'Email dibutuhkan',
                'valid_email' => 'Email tidak valid',
            ]
        ],
        'password' => [
            'rules' => 'required',
            'errors' => [
                'required' => 'Password dibutuhkan',
            ]
        ]
    ];

    public $forgotPassword = [
        'email' => [
            'rules' => 'required|valid_email',
            'errors' => [
                'required' => 'Email dibutuhkan',
                'valid_email' => 'Email tidak valid',
            ]
        ]
    ];

    public $resetPassword = [
        'token' => [
            'rules' => 'required',
            'errors' => [
                'required' => 'Token dibutuhkan',
            ]
        ],
        'password' => [
            'rules' => 'required|min_length[8]',
            'errors' => [
                'required' => 'Password dibutuhkan',
                'min_length' => 'Password harus terdiri dari minimal 8 karakter',
            ]
        ],
        'password_confirmation' => [
            'rules' => 'required|matches[password]',
            'errors' => [
                'required' => 'Konfirmasi password dibutuhkan',
                'matches' => 'Password tidak cocok',
            ]
        ]
    ];
}
