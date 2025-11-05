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
                ->select('id, role_name, created_at')
                ->where('deleted_at', null)
                ->findAll();

            foreach ($query as $role) {
                $role['created_at'] = date('Y-m-d', strtotime($role['created_at']));
            }

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

        $roleModel->delete($id);

        return $this->response->setStatusCode(ResponseInterface::HTTP_OK)
            ->setJSON(['message' => 'Role berhasil dihapus.']);
    }
}

