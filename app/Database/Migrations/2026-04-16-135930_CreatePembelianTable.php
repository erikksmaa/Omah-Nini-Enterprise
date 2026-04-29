<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembelianTable extends Migration
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
            'no_invoice' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
            'id_supplier' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tanggal_pembelian' => [
                'type' => 'DATE',
            ],
            'total_harga' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('no_invoice');
        $this->forge->addForeignKey('id_supplier', 'supplier', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('id_user', 'users', 'user_id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('pembelian');
    }

    public function down()
    {
        $this->forge->dropTable('pembelian');
    }
}