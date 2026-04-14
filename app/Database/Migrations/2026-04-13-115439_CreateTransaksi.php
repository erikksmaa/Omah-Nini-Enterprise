<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransaksi extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'no_invoice' => ['type' => 'VARCHAR', 'constraint' => 30, 'unique' => true],
            'id_user' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tanggal_transaksi' => ['type' => 'TIMESTAMP', null],
            'total_bayar' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'tipe_pembayaran' => ['type' => 'ENUM', 'constraint' => ['tunai', 'transfer', 'qris']],
            'status' => ['type' => 'ENUM', 'constraint' => ['selesai', 'batal']],
            'catatan' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('transaksi');
    }

    public function down()
    {
        //
    }
}
