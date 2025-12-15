<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductBatchesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'product_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'batch_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'expired_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'purchase_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => '0.00',
            ],
            'initial_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'current_stock' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'rack' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'row' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'slot' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'receipt_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('product_id');
        $this->forge->addUniqueKey('batch_code');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('product_batches');
    }

    public function down()
    {
        $this->forge->dropTable('product_batches');
    }
}
