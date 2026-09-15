<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQueueNumbers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'order_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'unique'     => true,
            ],
            'queue_date' => [
                'type' => 'DATE',
            ],
            'queue_number' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'formatted_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['queue_date', 'queue_number']);
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('queue_numbers', true);
    }

    public function down()
    {
        $this->forge->dropTable('queue_numbers', true);
    }
}