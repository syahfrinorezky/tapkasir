<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentFieldsToTransactions extends Migration
{
    public function up()
    {
        $fields = [
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'cash',
                'after' => 'change'
            ],
            'payment_status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'completed',
                'after' => 'payment_method'
            ],
            'snap_token' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'payment_status'
            ],
            'midtrans_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'snap_token'
            ],
        ];
        $this->forge->addColumn('transactions', $fields);
    }

    public function down()
    {
        $this->dropColumnSilently('transactions', ['payment_method', 'payment_status', 'snap_token', 'midtrans_id']);
    }

    private function dropColumnSilently(string $table, $columns)
    {
        try {
            $this->forge->dropColumn($table, $columns);
        } catch (\Throwable $e) {
        }
    }
}
