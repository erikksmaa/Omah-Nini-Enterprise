<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailTransaksiTable extends Migration
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
            'id_transaksi' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'id_produk' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'nama_produk' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_transaksi', 'transaksi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_produk', 'produk', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('detail_transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('detail_transaksi');
    }
}