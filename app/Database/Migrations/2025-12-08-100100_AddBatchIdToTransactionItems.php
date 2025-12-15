<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBatchIdToTransactionItems extends Migration
{
    public function up()
    {
        $fields = [
            'batch_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'product_id',
            ],
        ];

        $this->forge->addColumn('transaction_items', $fields);

        try {
            $this->db->query('ALTER TABLE `transaction_items` ADD CONSTRAINT `fk_transaction_items_batch` FOREIGN KEY (`batch_id`) REFERENCES `product_batches`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        } catch (\Throwable $e) {

        }
    }

    public function down()
    {
        try {
            $this->db->query('ALTER TABLE `transaction_items` DROP FOREIGN KEY `fk_transaction_items_batch`');
        } catch (\Throwable $e) {
        }
        $this->forge->dropColumn('transaction_items', 'batch_id');
    }
}
