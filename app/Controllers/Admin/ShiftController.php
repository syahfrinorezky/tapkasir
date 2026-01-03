<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ShiftModel;
use App\Models\CashierWorkModel;

class ShiftController extends BaseController
{
    public function index()
    {
        if ($this->request->isAJAX()) {
            return $this->data();
        }

        return view('app/admin/master-data/shift');
    }

    public function data()
    {
        $userModel = new UserModel();
        $shiftModel = new ShiftModel();

        $cashiers = $userModel
            ->select('users.id, users.nama_lengkap, users.email, roles.role_name, shifts.id as shift_id, shifts.name as shift_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('cashier_works', 'cashier_works.user_id = users.id AND cashier_works.status = "active"', 'left')
            ->join('shifts', 'shifts.id = cashier_works.shift_id', 'left')
            ->where('users.role_id', 2)
            ->where('users.status', 'approved')
            ->where('users.deleted_at', null)
            ->findAll();

        $shifts = $shiftModel->where('status !=', 'deleted')->findAll();

        return $this->response->setJSON([
            'cashiers' => $cashiers,
            'shifts' => $shifts,
        ]);
    }

    public function updateCashierShift($userId)
    {
        $shiftModel = new ShiftModel();
        $cashierWorkModel = new CashierWorkModel();
        $shiftId = $this->request->getPost('shift_id');

        if (!$shiftId) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Shift ID tidak boleh kosong.']);
        }

        $shift = $shiftModel->where('id', $shiftId)
            ->where('status', 'active')
            ->first();

        if (!$shift) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Shift tidak aktif atau tidak ditemukan.'
            ]);
        }

        $cashierWorkModel
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->set(['status' => 'inactive'])
            ->update();

        $cashierWorkModel->insert([
            'user_id' => $userId,
            'shift_id' => $shiftId,
            'work_date' => date('Y-m-d'),
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['message' => 'Shift kasir berhasil diperbarui']);
    }

    private function isValidShiftTime($start_time, $end_time)
    {
        $start = strtotime($start_time);
        $end = strtotime($end_time);

        if ($end < $start) {
            return true;
        }

        return $end > $start;
    }

    public function addShift()
    {
        $shiftModel = new ShiftModel();

        $data = $this->request->getJSON();

        $validation = \Config\Services::validation();
        $rules = (new \App\Validation\ShiftRules())->create;
        $validation->setRules($rules);

        $input = is_object($data) ? (array) $data : (array) $data;
        if (!$validation->run($input)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Validasi gagal.',
                'validation' => $validation->getErrors(),
            ]);
        }

        $name = $data->name ?? null;
        $start_time = $data->start_time ?? null;
        $end_time = $data->end_time ?? null;
        $status = $data->status ?? null;

        if (!$this->isValidShiftTime($start_time, $end_time)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Jam selesai harus setelah jam mulai. Untuk shift melewati tengah malam, gunakan format 24 jam.'
            ]);
        }

        $shiftModel->insert([
            'name' => $name,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'status' => $status ?? 'active',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['message' => 'Shift baru berhasil ditambahkan.']);
    }

    public function editShift($id)
    {
        $shiftModel = new ShiftModel();
        $shift = $shiftModel->find($id);

        if (!$shift) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Shift tidak ditemukan.']);
        }

        $data = $this->request->getJSON();

        $validation = \Config\Services::validation();
        $rules = (new \App\Validation\ShiftRules())->update;
        $validation->setRules($rules);

        $input = is_object($data) ? (array) $data : (array) $data;
        if (!$validation->run($input)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Validasi gagal.',
                'validation' => $validation->getErrors(),
            ]);
        }

        $name = $data->name ?? null;
        $start_time = $data->start_time ?? null;
        $end_time = $data->end_time ?? null;
        $status = $data->status ?? null;

        if ($name) {
            $existingShift = $shiftModel->where('name', $name)->where('id !=', $id)->first();
            if ($existingShift) {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Nama shift sudah digunakan.'
                ]);
            }
        }

        $shiftModel->update($id, [
            'name' => $name,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'status' => $status ?? 'active',
        ]);

        return $this->response->setJSON(['message' => 'Shift berhasil diperbarui.']);
    }

    public function deleteShift($id)
    {
        $shiftModel = new ShiftModel();
        $shift = $shiftModel->find($id);

        if (!$shift) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Shift tidak ditemukan.']);
        }

        $shiftModel->delete($id);

        return $this->response->setJSON(['message' => 'Shift berhasil dihapus.']);
    }

    public function trashData()
    {
        $shiftModel = new ShiftModel();
        $shifts = $shiftModel->onlyDeleted()->findAll();
        return $this->response->setJSON(['shifts' => $shifts]);
    }

    public function restore($id = null)
    {
        $shiftModel = new ShiftModel();
        $json = $this->request->getJSON();
        $ids = $id ? [$id] : ($json->ids ?? []);

        if (empty($ids)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang dipilih']);
        }

        $shiftModel->builder()->whereIn('id', $ids)->update(['deleted_at' => null]);
        return $this->response->setJSON(['message' => 'Data berhasil dipulihkan']);
    }

    public function deletePermanent($id = null)
    {
        $shiftModel = new ShiftModel();
        $json = $this->request->getJSON();
        $ids = $id ? [$id] : ($json->ids ?? []);

        if (empty($ids)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang dipilih']);
        }

        $shiftModel->delete($ids, true);
        return $this->response->setJSON(['message' => 'Data berhasil dihapus permanen']);
    }

    public function trashCashiersData()
    {
        $cashierWorkModel = new CashierWorkModel();
        $cashiers = $cashierWorkModel
            ->select('cashier_works.*, users.nama_lengkap')
            ->join('users', 'users.id = cashier_works.user_id')
            ->onlyDeleted()
            ->findAll();
        return $this->response->setJSON(['cashiers' => $cashiers]);
    }

    public function restoreCashier($id = null)
    {
        $cashierWorkModel = new CashierWorkModel();
        $json = $this->request->getJSON();
        $ids = $id ? [$id] : ($json->ids ?? []);

        if (empty($ids)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang dipilih']);
        }

        $cashierWorkModel->builder()->whereIn('id', $ids)->update(['deleted_at' => null]);
        return $this->response->setJSON(['message' => 'Data berhasil dipulihkan']);
    }

    public function deletePermanentCashier($id = null)
    {
        $cashierWorkModel = new CashierWorkModel();
        $json = $this->request->getJSON();
        $ids = $id ? [$id] : ($json->ids ?? []);

        if (empty($ids)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang dipilih']);
        }

        $cashierWorkModel->delete($ids, true);
        return $this->response->setJSON(['message' => 'Data berhasil dihapus permanen']);
    }
}

