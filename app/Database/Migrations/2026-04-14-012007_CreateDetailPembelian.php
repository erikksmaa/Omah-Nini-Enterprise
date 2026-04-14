<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailPembelian extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_pembelian' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_produk' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama_produk' => ['type' => 'VARCHAR', 'constraint' => 100], // Snapshot [cite: 19]
            'jumlah' => ['type' => 'INT', 'constraint' => 11],
            'harga_beli' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'subtotal' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('detail_pembelian');
    }

    public function down()
    {
        //
    }
}
