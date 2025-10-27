<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhotoColumnToProductTable extends Migration
{
    public function up()
    {
        $fields = [
            'photo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'id'
            ]
        ];

        $this->forge->addColumn('products', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'photo');
    }
}
