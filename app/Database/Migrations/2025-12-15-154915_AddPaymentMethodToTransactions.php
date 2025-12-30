<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentMethodToTransactions extends Migration
{
    public function up()
    {
        $fields = [
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'cash',
                'after' => 'change',
            ],
            'payment_status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'paid',
                'after' => 'payment_method',
            ],
            'snap_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'payment_status',
            ],
        ];

        foreach ($fields as $fieldName => $fieldDef) {
            $this->addColumnSilently('transactions', $fieldName, $fieldDef);
        }
    }

    public function down()
    {
        $this->dropColumnSilently('transactions', ['payment_method', 'payment_status', 'snap_token']);
    }

    private function addColumnSilently(string $table, string $fieldName, array $fieldDef)
    {
        try {
            $this->forge->addColumn($table, [$fieldName => $fieldDef]);
        } catch (\Throwable $e) {
        }
    }

    private function dropColumnSilently(string $table, $columns)
    {
        try {
            $this->forge->dropColumn($table, $columns);
        } catch (\Throwable $e) {
        }
    }
}
