<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use CodeIgniter\HTTP\ResponseInterface;

class RoleController extends BaseController
{
    public function index()
    {
        $roleModel = new RoleModel();

        if ($this->request->isAJAX()) {
            $query = $roleModel
                ->select('roles.id, roles.role_name, roles.created_at, COUNT(users.id) as user_count')
                ->join('users', 'users.role_id = roles.id AND users.deleted_at IS NULL', 'left')
                ->where('roles.deleted_at', null)
                ->groupBy('roles.id')
                ->findAll();

            return $this->response->setJSON($query);
        }

        return view('app/admin/usermanagement');
    }

    public function addRole()
    {
        $roleModel = new RoleModel();

        $data = $this->request->getJSON();

        $roleModel->insert([
            'role_name' => $data->role_name,
        ]);

        return $this->response->setJSON(['message' => 'Role baru berhasil ditambahkan.']);
    }

    public function editRole($id)
    {
        $roleModel = new RoleModel();

        $data = $this->request->getJSON();

        $roleModel->update($id, [
            'role_name' => $data->role_name,
        ]);

        return $this->response->setStatusCode(ResponseInterface::HTTP_OK)
            ->setJSON(['message' => 'Nama role berhasil diperbarui.']);
    }

    public function deleteRole($id)
    {
        $roleModel = new RoleModel();

        $role = $roleModel->find($id);

        if (!$role) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setJSON(['message' => 'Role tidak ditemukan.']);
        }

        $userModel = new \App\Models\UserModel();
        $usageCount = $userModel
            ->where('role_id', $id)
            ->where('deleted_at', null)
            ->countAllResults();

        if ($usageCount > 0) {
            return $this->response->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST)
                ->setJSON(['message' => 'Tidak dapat menghapus role ini karena masih digunakan oleh ' . $usageCount . ' pengguna.']);
        }

        $roleModel->delete($id);

        return $this->response->setStatusCode(ResponseInterface::HTTP_OK)
            ->setJSON(['message' => 'Role berhasil dihapus.']);
    }

    public function trashData()
    {
        $roleModel = new RoleModel();
        $roles = $roleModel->onlyDeleted()->findAll();
        return $this->response->setJSON(['roles' => $roles]);
    }

    public function restore($id = null)
    {
        $roleModel = new RoleModel();
        $json = $this->request->getJSON();
        $ids = $id ? [$id] : ($json->ids ?? []);

        if (empty($ids)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang dipilih']);
        }

        $roleModel->builder()->whereIn('id', $ids)->update(['deleted_at' => null]);
        return $this->response->setJSON(['message' => 'Data berhasil dipulihkan']);
    }

    public function deletePermanent($id = null)
    {
        $roleModel = new RoleModel();
        $json = $this->request->getJSON();
        $ids = $id ? [$id] : ($json->ids ?? []);

        if (empty($ids)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang dipilih']);
        }

        $roleModel->delete($ids, true);
        return $this->response->setJSON(['message' => 'Data berhasil dihapus permanen']);
    }
}

