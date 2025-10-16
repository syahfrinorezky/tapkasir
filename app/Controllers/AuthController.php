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

    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = (new \App\Validation\UserRules)->login;

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('validation', $this->validator->getErrors());
            }

            try {
                $userModel = new UserModel();

                $user = $userModel->where('email', $this->request->getPost('email'))->first();

                if (!$user || !password_verify($this->request->getPost('password'), $user['password'])) {
                    return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
                }

                $userSession = [
                    'user_id' => $user['id'],
                    'nama_lengkap' => $user['nama_lengkap'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'isLoggedIn' => true,
                ];

                session()->set($userSession);

                return redirect()->to('/');
            } catch (\Throwable $th) {
                return redirect()->back()->withInput()->with('error', 'Gagal masuk. Silahkan coba lagi.');
            }
        }
        return view('app/auth/login');
    }

    public function register()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = (new \App\Validation\UserRules)->registration;

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('validation', $this->validator->getErrors());
            }

            try {
                $userModel = new UserModel();

                $userData = [
                    'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                    'email' => $this->request->getPost('email'),
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
                    'role' => 'cashier',
                ];

                $userModel->insert($userData);

                return redirect()->to('/')->with('success', 'Pendaftaran berhasil. Silahkan masuk.');
            } catch (\Throwable $th) {
                return redirect()->back()->withInput()->with('error', 'Pendaftaran gagal. Silahkan coba lagi.');
            }
        }

        return view('app/auth/register');
    }
}
