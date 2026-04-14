<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogStok extends Migration
{
    public function up()
    {
$this->forge->addField([
    'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
    'id_produk'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
    'id_user'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
    'tipe_ref'         => ['type' => 'VARCHAR', 'constraint' => 20],
    'id_ref'           => ['type' => 'INT', 'constraint' => 11, 'null' => true],
    'jumlah_sebelum'   => ['type' => 'INT', 'constraint' => 11],
    'jumlah_perubahan' => ['type' => 'INT', 'constraint' => 11],
    'jumlah_sesudah'   => ['type' => 'INT', 'constraint' => 11],
    'aktivitas'        => ['type' => 'VARCHAR', 'constraint' => 30],
    'created_at'       => ['type' => 'TIMESTAMP', 'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP')],
]);
$this->forge->addKey('id', true);
$this->forge->createTable('log_stok');
    }

    public function down()
    {
        //
    }
}
