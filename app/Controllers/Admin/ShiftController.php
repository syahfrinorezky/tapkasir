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

        $name = $data->name ?? null;
        $start_time = $data->start_time ?? null;
        $end_time = $data->end_time ?? null;
        $status = $data->status ?? null;

        if (!$name || !$start_time || !$end_time) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Semua field wajib diisi.']);
        }

        if (!$this->isValidShiftTime($start_time, $end_time)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Jam selesai harus setelah jam mulai. Untuk shift melewati tengah malam, gunakan format 24 jam.'
            ]);
        }

        $existingShift = $shiftModel->where('name', $name)->first();
        if ($existingShift) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Nama shift sudah digunakan.'
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

        $name = $data->name ?? null;
        $start_time = $data->start_time ?? null;
        $end_time = $data->end_time ?? null;
        $status = $data->status ?? null;

        if (!$name || !$start_time || !$end_time) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Semua field wajib diisi.']);
        }

        $existingShift = $shiftModel->where('name', $name)->where('id !=', $id)->first();
        if ($existingShift) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Nama shift sudah digunakan.'
            ]);
        }

        $shiftModel->update($id, [
            'name' => $name,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'status' => $status ?? 'active',
        ]);

        return $this->response->setJSON(['message' => 'Shift berhasil diperbarui.']);
    }
}
