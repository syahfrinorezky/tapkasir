<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function __construct()
    {
        helper('form');
    }

    public function register()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = (new \App\Validation\UserRules)->registration;

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('validation', $this->validator->getErrors());
            }

            $userModel = new UserModel();

            $userData = [
                'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
                'role' => 'cashier',
            ];

            try {
                $userModel->insert($userData);

                return redirect()->to('/')->with('success', 'Pendaftaran berhasil. Silahkan masuk.');
            } catch (\Throwable $th) {
                return redirect()->back()->withInput()->with('error', 'Pendaftaran gagal. Silahkan coba lagi.');
            }
        }

        return view('app/auth/register');
    }
}
