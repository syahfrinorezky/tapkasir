<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CleanupProductBatchesColumns extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('product_batches', ['rack', 'row', 'slot']);
    }

    public function down()
    {
        $this->forge->addColumn('product_batches', [
            'rack' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'row' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'slot' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
        ]);
    }
}
