<?php

namespace App\Filters;

use App\Models\CashierWorkModel;
use App\Models\ShiftModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

class ShiftGuardFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return;
        }

        $role = $session->get('role_name');
        if (strtolower((string) $role) !== 'kasir') {
            return;
        }

        $userId = (int) $session->get('user_id');
        if (!$userId) {
            return;
        }

        $tz = config('App')->appTimezone ?? 'Asia/Makassar';
        $now = Time::now($tz);

        $workModel = new CashierWorkModel();
        $shiftModel = new ShiftModel();

        $work = $workModel
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->orderBy('work_date', 'DESC')
            ->first();

        if (!$work) {
            return;
        }

        $shift = $shiftModel->find($work['shift_id'] ?? 0);
        if (!$shift) {
            return;
        }

        $workDate = $work['work_date'] ?? $now->toDateString();
        $startTime = $shift['start_time'] ?? '00:00:00';
        $endTime   = $shift['end_time'] ?? '23:59:59';

        $start = Time::parse($workDate . ' ' . $startTime, $tz);
        $end = Time::parse($workDate . ' ' . $endTime, $tz);

        if ($end->isBefore($start)) {
            $end = $end->addDays(1);
        }

        if ($now->isAfter($end)) {
            try {
                $workModel->update($work['id'], ['status' => 'inactive']);
            } catch (\Throwable $e) {
            }

            $session->destroy();
            return redirect()->to('/')
                ->with('error', 'Shift Anda telah berakhir. Silakan masuk kembali.');
        }

        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
