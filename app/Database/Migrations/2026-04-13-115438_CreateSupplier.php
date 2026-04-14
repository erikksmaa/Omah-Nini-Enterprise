<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSupplier extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'kontak' => ['type' => 'VARCHAR', 'constraint' => 20],
            'email' => ['type' => 'VARCHAR', 'constraint' => 90, 'null' => true],
            'alamat' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('supplier');
    }

    public function down()
    {
        //
    }
}
