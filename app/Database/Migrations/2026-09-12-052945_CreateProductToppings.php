<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductToppings extends Migration
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
            'product_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'topping_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['product_id', 'topping_id']);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('topping_id', 'toppings', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('product_toppings', true);
    }

    public function down()
    {
        $this->forge->dropTable('product_toppings', true);
    }
}