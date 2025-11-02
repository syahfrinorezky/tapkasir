<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnNoTransaction extends Migration
{
    public function up()
    {
        $fields = [
            'no_transaction' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'after' => 'id',
                'null' => true,                
            ],
        ];
        
        $this->forge->addColumn('transactions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'no_transaction');
    }
}
