<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CashierWorkModel;
use App\Models\ShiftModel;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function __construct()
    {
        helper('form');
    }

    private function isWithinShiftTime($startTime, $endTime)
    {
        $currentTime = date('H:i');

        if (strtotime($endTime) < strtotime($startTime)) {
            return $currentTime >= $startTime || $currentTime <= $endTime;
        } else {
            return $currentTime >= $startTime && $currentTime <= $endTime;
        }
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
                $cashierWorkModel = new CashierWorkModel(); 
                $shiftModel = new ShiftModel();

                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                $user = $userModel
                    ->select('users.*, roles.role_name')
                    ->join('roles', 'roles.id = users.role_id')
                    ->where('email', $email)
                    ->first();

                if (!$user) {
                    return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
                }

                if (!password_verify($password, $user['password'])) {
                    return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
                }

                if ($user['status'] !== 'approved') {
                    return redirect()->back()->withInput()->with('error', 'Akun Anda belum disetujui. Silahkan hubungi admin.');
                }

                if ($user['role_name'] === 'kasir') {
                    $currentShift = $cashierWorkModel
                        ->select('cashier_works.*, shifts.name, shifts.start_time, shifts.end_time')
                        ->join('shifts', 'shifts.id = cashier_works.shift_id')
                        ->where('cashier_works.user_id', $user['id'])
                        ->where('cashier_works.status', 'active')
                        ->where('shifts.status', 'active')
                        ->first();

                    if (!$currentShift) {
                        return redirect()->back()->withInput()->with('error', 'Anda tidak memiliki shift aktif. Silahkan hubungi admin.');
                    }

                    if (!$this->isWithinShiftTime($currentShift['start_time'], $currentShift['end_time'])) {
                        $currentTime = date('H:i');
                        $shiftTime = $currentShift['start_time'] . ' - ' . $currentShift['end_time'];

                        return redirect()->back()->withInput()->with(
                            'error',
                            "Anda hanya dapat login pada jam({$shiftTime}). Silahkan login sesuai jam shift Anda atau hubungi admin."
                        );
                    }
                }

                $userSession = [
                    'user_id' => $user['id'],
                    'nama_lengkap' => $user['nama_lengkap'],
                    'email' => $user['email'],
                    'role_name' => $user['role_name'],
                    'isLoggedIn' => true,
                ];

                if ($user['role_name'] === 'kasir' && isset($currentShift)) {
                    $userSession['shift_id'] = $currentShift['shift_id'];
                    $userSession['shift_name'] = $currentShift['name'];
                    $userSession['shift_time'] = $currentShift['start_time'] . ' - ' . $currentShift['end_time'];
                }

                session()->set($userSession);

                if ($user['role_name'] === 'admin') {
                    return redirect()->to('/admin/dashboard');
                } else {
                    return redirect()->to('/cashier/dashboard');
                }
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
                    'role_id' => 2,
                    'status' => 'pending',
                ];

                $userModel->insert($userData);

                return redirect()->to('/')->with('success', 'Pendaftaran berhasil. Silahkan menunggu konfirmasi.');
            } catch (\Throwable $th) {
                return redirect()->back()->withInput()->with('error', 'Pendaftaran gagal. Silahkan coba lagi.');
            }
        }

        return view('app/auth/register');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
