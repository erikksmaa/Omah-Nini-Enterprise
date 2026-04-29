<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailReturPenjualanTable extends Migration
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
            'id_retur' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_detail_transaksi' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_produk' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'nama_produk' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'jumlah' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'harga_jual' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_retur', 'retur_penjualan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_retur_penjualan');
    }

    public function down()
    {
        $this->forge->dropTable('detail_retur_penjualan');
    }
}