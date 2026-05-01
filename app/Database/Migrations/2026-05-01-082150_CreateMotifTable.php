<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMotifTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_supplier' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'nama_motif' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['id_supplier', 'nama_motif'], 'unique_motif_per_supplier');
        $this->forge->addForeignKey('id_supplier', 'supplier', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('motif');
    }

    public function down()
    {
        $this->forge->dropTable('motif');
    }
}