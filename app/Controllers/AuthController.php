<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CashierWorkModel;
use App\Models\PasswordResetModel;
use App\Models\ShiftModel;
use App\Models\UserModel;
use App\Validation\UserRules;

class AuthController extends BaseController
{
    public function __construct()
    {
        helper('form');
    }

    private function isWithinShiftTime($startTime, $endTime)
    {
        $now = strtotime(date('H:i:s'));
        $start = strtotime($startTime);
        $end = strtotime($endTime);

        if ($start <= $end) {
            return $now >= $start && $now <= $end;
        }

        return $now >= $start || $now <= $end;
    }

    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            $rules = (new \App\Validation\UserRules)->login;

            if (!$this->validate($rules)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validasi gagal.',
                        'errors' => $this->validator->getErrors(),
                        'csrf_token' => csrf_hash()
                    ]);
                }
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
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Email atau password salah.',
                            'csrf_token' => csrf_hash()
                        ]);
                    }
                    return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
                }

                if (!password_verify($password, $user['password'])) {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Email atau password salah.',
                            'csrf_token' => csrf_hash()
                        ]);
                    }
                    return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
                }

                if ($user['status'] !== 'approved') {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Akun Anda belum disetujui. Silahkan hubungi admin.',
                            'csrf_token' => csrf_hash()
                        ]);
                    }
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
                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON([
                                'success' => false,
                                'message' => 'Anda tidak memiliki shift aktif. Silahkan hubungi admin.',
                                'csrf_token' => csrf_hash()
                            ]);
                        }
                        return redirect()->back()->withInput()->with('error', 'Anda tidak memiliki shift aktif. Silahkan hubungi admin.');
                    }

                    if (!$this->isWithinShiftTime($currentShift['start_time'], $currentShift['end_time'])) {
                        $currentTime = date('H:i');
                        $shiftTime = $currentShift['start_time'] . ' - ' . $currentShift['end_time'];

                        if ($this->request->isAJAX()) {
                            return $this->response->setJSON([
                                'success' => false,
                                'message' => "Anda hanya dapat login pada jam({$shiftTime}). Silahkan login sesuai jam shift Anda atau hubungi admin.",
                                'csrf_token' => csrf_hash()
                            ]);
                        }
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

                $redirectUrl = ($user['role_name'] === 'admin') ? '/admin/dashboard' : '/cashier/transactions';

                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Login berhasil.',
                        'redirect' => $redirectUrl,
                        'csrf_token' => csrf_hash()
                    ]);
                }

                return redirect()->to($redirectUrl);
            } catch (\Throwable $th) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal masuk. Silahkan coba lagi.',
                        'csrf_token' => csrf_hash()
                    ]);
                }
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
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validasi gagal.',
                        'errors' => $this->validator->getErrors(),
                        'csrf_token' => csrf_hash()
                    ]);
                }
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

                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Pendaftaran berhasil. Silahkan menunggu konfirmasi.',
                        'redirect' => '/',
                        'csrf_token' => csrf_hash()
                    ]);
                }

                return redirect()->to('/')->with('success', 'Pendaftaran berhasil. Silahkan menunggu konfirmasi.');
            } catch (\Throwable $th) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Pendaftaran gagal. Silahkan coba lagi.',
                        'csrf_token' => csrf_hash()
                    ]);
                }
                return redirect()->back()->withInput()->with('error', 'Pendaftaran gagal. Silahkan coba lagi.');
            }
        }

        return view('app/auth/register');
    }

    public function forgotPassword()
    {
        return view('app/auth/forgot-password');
    }

    public function sendResetLink()
    {
        $rules = (new \App\Validation\UserRules)->forgotPassword;

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors(),
                'csrf_token' => csrf_token()
            ]);
        }

        $email = $this->request->getPost('email');
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email tidak ditemukan',
                'csrf_token' => csrf_token()
            ]);
        }

        $token = bin2hex(random_bytes(32));
        $resetModel = new PasswordResetModel();

        $resetModel->where('email', $email)->delete();
        $resetModel->where('token', $token)->delete();
        $resetModel->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $emailService = \Config\Services::email();
        $emailService->setMailType('html');
        $emailService->setTo($email);
        $emailService->setSubject('Reset Password - TAPKASIR');

        $message = view('email/reset_password', [
            'link' => base_url("reset-password/$token"),
            'nama' => $user['nama_lengkap'],
        ]);

        $emailService->setMessage($message);

        if (!$emailService->send()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengirim email. ',
                'csrf_token' => csrf_token()
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Email reset password berhasil dikirim',
            'csrf_token' => csrf_token()
        ]);
    }

    public function resetPassword($token)
    {
        if (!$token) {
            return redirect()->to('/');
        }

        return view('app/auth/reset-password', [
            'token' => $token
        ]);
    }

    public function attemptReset()
    {
        $rules = (new UserRules())->resetPassword;

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors(),
                'csrf_token' => csrf_token()
            ]);
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $passwordConfirmation = $this->request->getPost('password_confirmation');

        if ($password !== $passwordConfirmation) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Password tidak cocok',
                'csrf_token' => csrf_token()
            ]);
        }

        $resetModel = new PasswordResetModel();
        $reset = $resetModel->where('token', $token)->first();

        if (!$reset) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Token tidak valid atau sudah kadaluarsa',
                'csrf_token' => csrf_token()
            ]);
        }

        $createdAt = strtotime($reset['created_at']);
        $expiryTime = $createdAt + (60 * 60);

        if (time() > $expiryTime) {
            $resetModel->where('token', $token)->delete();

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Token sudah kadaluarsa. Silakan request ulang.',
                'csrf_token' => csrf_token()
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $reset['email'])->first();

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User tidak ditemukan',
                'csrf_token' => csrf_token()
            ]);
        }

        $userModel->update($user['id'], [
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ]);

        $resetModel->where('email', $reset['email'])->delete();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Password berhasil direset',
            'redirect' => base_url('masuk'),
            'csrf_token' => csrf_token()
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}

