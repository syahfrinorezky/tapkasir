<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();

        if ($this->request->isAJAX()) {
            $type = $this->request->getGet('type');

            $query = $userModel
                ->select('users.id, users.nama_lengkap, users.email, users.status, users.role_id, roles.role_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('users.deleted_at', null);

            if ($type === 'pending') {
                $query->where('users.status', 'pending');
            } elseif ($type === 'approved') {
                $query->where('users.status', 'approved');
            }

            $users = $query->findAll();
            return $this->response->setJSON($users);
        }

        return view('app/admin/usermanagement');
    }

    public function updateStatus($id)
    {
        $userModel = new UserModel();

        $user = $userModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'User tidak ditemukan']);
        }

        $req = $this->request->getJSON();
        $status = $req->status ?? null;

        if (!in_array($status, ['approved', 'rejected'])) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Status tidak valid']);
        }

        $userModel->update($id, ['status' => $status]);

        return $this->response->setJSON(['message' => 'Status pengguna berhasil diperbarui']);
    }

    public function updateInfo($id)
    {
        $userModel = new UserModel();

        $user = $userModel->find($id);

        if ($user) {
            $req = $this->request->getJSON();
            $nama = $req->nama_lengkap ?? null;
            $email = $req->email ?? null;
            $role = $req->role_id ?? null;

            $data = [];
            if ($nama && $nama !== $user['nama_lengkap']) {
                $data['nama_lengkap'] = $nama;
            }
            if ($email && $email !== $user['email']) {
                $data['email'] = $email;
            }
            if ($role && $role !== $user['role_id']) {
                $data['role_id'] = $role;
            }

            if (empty($data)) {
                return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang diubah']);
            }

            $userModel->update($id, $data);
            return $this->response->setJSON(['message' => 'Informasi pengguna berhasil diperbarui']);
        }

        return $this->response->setStatusCode(404)->setJSON(['message' => 'User tidak ditemukan']);
    }

    public function delete($id) {
        $userModel = new UserModel();

        $user = $userModel->find($id);

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'User tidak ditemukan']);
        }

        $userModel->delete($id);
        
        return $this->response->setJSON(['message' => 'User berhasil dihapus']);
    }
}
