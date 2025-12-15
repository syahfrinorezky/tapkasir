<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLocationIdToProductBatches extends Migration
{
    public function up()
    {
        $this->forge->addColumn('product_batches', [
            'location_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'batch_code',
            ],
        ]);
        
        $this->db->query('ALTER TABLE `product_batches` ADD CONSTRAINT `fk_batches_location` FOREIGN KEY (`location_id`) REFERENCES `storage_locations`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('product_batches', 'fk_batches_location');
        $this->forge->dropColumn('product_batches', 'location_id');
    }
}
