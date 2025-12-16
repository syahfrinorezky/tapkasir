<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentMethodToTransactions extends Migration
{
    public function up()
    {
        $fields = [
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'cash',
                'after'      => 'change',
            ],
            'payment_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'paid',
                'after'      => 'payment_method',
            ],
            'snap_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'payment_status',
            ],
        ];

        foreach ($fields as $fieldName => $fieldDef) {
            if (! $this->db->fieldExists($fieldName, 'transactions')) {
                $this->forge->addColumn('transactions', [$fieldName => $fieldDef]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', ['payment_method', 'payment_status', 'snap_token']);
    }
}
