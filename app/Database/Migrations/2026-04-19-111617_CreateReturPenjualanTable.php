<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReturPenjualanTable extends Migration
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
            'no_retur' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'unique' => true,
            ],
            'id_transaksi' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tanggal_retur' => [
                'type' => 'DATE',
            ],
            'total_retur' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'alasan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_transaksi', 'transaksi', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('id_user', 'users', 'user_id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('retur_penjualan');
    }

    public function down()
    {
        $this->forge->dropTable('retur_penjualan');
    }
}