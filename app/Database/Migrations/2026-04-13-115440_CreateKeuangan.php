<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKeuangan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_user' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tipe' => ['type' => 'ENUM', 'constraint' => ['pemasukan', 'pengeluaran']],
            'kategori' => ['type' => 'VARCHAR', 'constraint' => 50],
            'tipe_ref' => ['type' => 'VARCHAR', 'constraint' => 20],
            'id_ref' => ['type' => 'INT', 'constraint' => 11],
            'jumlah' => ['type' => 'DECIMAL', 'constraint' => '15,2'],
            'tanggal_transaksi' => ['type' => 'DATE'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('keuangan');
    }

    public function down()
    {
        //
    }
}
