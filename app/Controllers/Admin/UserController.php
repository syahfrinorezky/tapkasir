<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    public function index()
    {
        $userModel = new \App\Models\UserModel();

        $totalUsers = $userModel
            ->where('status', 'approved')
            ->countAllResults();

        $userModel->resetQuery();

        $userList = $userModel
            ->select('users.*, roles.role_name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.status', 'approved')
            ->findAll();

        $userModel->resetQuery();

        $pendingUsersList = $userModel
            ->where('status', 'pending')
            ->findAll();


        $data = [
            'totalUsers' => $totalUsers,
            'userList' => $userList,
            'pendingUsersList' => $pendingUsersList,
        ];

        return view('app/admin/usermanagement', $data);
    }

    public function show($id)
    {
        $userModel = new \App\Models\UserModel();

        $user = $userModel
            ->select('users.*, roles.role_name as role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->find($id);

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'User tidak ditemukan']);
        }

        return $this->response->setStatusCode(200)->setJSON($user);
    }
}
