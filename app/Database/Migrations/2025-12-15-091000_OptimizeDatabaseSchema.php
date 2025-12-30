<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OptimizeDatabaseSchema extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('products');
        if (in_array('stock', $fields)) {
            $this->forge->dropColumn('products', 'stock');
        }

        $this->db->query("ALTER TABLE `restock_requests` ADD CONSTRAINT `restock_requests_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE `restock_requests` ADD CONSTRAINT `restock_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE `restock_requests` ADD CONSTRAINT `restock_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE");

        $this->db->query("CREATE INDEX `idx_transactions_date` ON `transactions` (`transaction_date`)");
        $this->db->query("CREATE INDEX `idx_transactions_user` ON `transactions` (`user_id`)");

        $this->db->query("UPDATE `transactions` SET `no_transaction` = CONCAT('TAP-OLD-', id) WHERE `no_transaction` IS NULL");

        $this->forge->modifyColumn('transactions', [
            'no_transaction' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
        ]);
        $this->db->query("ALTER TABLE `transactions` ADD UNIQUE KEY `transactions_no_transaction_unique` (`no_transaction`)");

        $tables = ['users', 'products', 'product_batches', 'transactions', 'transaction_items', 'restock_requests', 'cashier_works'];

        foreach ($tables as $table) {
            $tableFields = $this->db->getFieldNames($table);

            if (in_array('created_at', $tableFields)) {
                $this->db->query("ALTER TABLE `$table` MODIFY `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP");
            }
            if (in_array('updated_at', $tableFields)) {
                $this->db->query("ALTER TABLE `$table` MODIFY `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
            }
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('products');
        if (!in_array('stock', $fields)) {
            $this->forge->addColumn('products', [
                'stock' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false,
                    'default' => 0,
                ],
            ]);
        }

        $this->tryQuery("ALTER TABLE `restock_requests` DROP FOREIGN KEY `restock_requests_product_id_foreign`");
        $this->tryQuery("ALTER TABLE `restock_requests` DROP FOREIGN KEY `restock_requests_user_id_foreign`");
        $this->tryQuery("ALTER TABLE `restock_requests` DROP FOREIGN KEY `restock_requests_approved_by_foreign`");

        $this->tryQuery("DROP INDEX `idx_transactions_date` ON `transactions`");
        $this->tryQuery("DROP INDEX `idx_transactions_user` ON `transactions`");

        $this->tryQuery("ALTER TABLE `transactions` DROP INDEX `transactions_no_transaction_unique`");

        $this->forge->modifyColumn('transactions', [
            'no_transaction' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
        ]);

        $tables = ['users', 'products', 'product_batches', 'transactions', 'transaction_items', 'restock_requests', 'cashier_works'];
        foreach ($tables as $table) {
            $tableFields = $this->db->getFieldNames($table);

            if (in_array('created_at', $tableFields)) {
                $this->db->query("ALTER TABLE `$table` MODIFY `created_at` DATETIME NULL");
            }
            if (in_array('updated_at', $tableFields)) {
                $this->db->query("ALTER TABLE `$table` MODIFY `updated_at` DATETIME NULL");
            }
        }
    }

    private function tryQuery(string $query)
    {
        try {
            $this->db->query($query);
        } catch (\Throwable $e) {
        }
    }
}
