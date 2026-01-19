<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RefactorTransactionStatus extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('status', 'transactions')) {
            $this->forge->dropColumn('transactions', 'status');
        }

        $fields = [
            'payment_status' => [
                'name'       => 'payment_status',
                'type'       => 'ENUM',
                'constraint' => ['pending', 'paid', 'cancelled', 'expired', 'failed'],
                'default'    => 'pending',
                'null'       => false,
            ],
        ];
        $this->forge->modifyColumn('transactions', $fields);
    }

    public function down()
    {
        $fields = [
            'payment_status' => [
                'name'       => 'payment_status',
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'pending',
            ],
        ];
        $this->forge->modifyColumn('transactions', $fields);

        if (!$this->db->fieldExists('status', 'transactions')) {
             $fields = [
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'pending',
                ],
            ];
            $this->forge->addColumn('transactions', $fields);
        }
    }
}
