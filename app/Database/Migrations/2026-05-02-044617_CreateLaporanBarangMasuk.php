<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLaporanBarangMasuk extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'no_laporan' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'unique'     => true,
            ],
            'id_user' => [
                'type'       => 'INT',
                'unsigned'   => true,
            ],
            'tanggal_awal' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_akhir' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'id_supplier' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
            'nama_file' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => null,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('id_user', 'users', 'user_id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('id_supplier', 'supplier', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('laporan_barang_masuk');
    }

    public function down()
    {
        $this->forge->dropTable('laporan_barang_masuk');
    }
}