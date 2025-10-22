<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ShiftsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Shift Siang',
                'start_time' => '06:00:00',
                'end_time' => '17:59:00',
            ],
            [
                'name' => 'Shift Malam',
                'start_time' => '18:00:00',
                'end_time' => '05:59:00',
            ],
        ];
        
        $this->db->table('shifts')->insertBatch($data);
    }
}
