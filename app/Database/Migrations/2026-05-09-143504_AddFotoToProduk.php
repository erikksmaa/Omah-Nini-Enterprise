<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoToProduk extends Migration
{
    public function up()
    {
        // Cek apakah kolom 'foto' sudah ada
        if (!$this->db->fieldExists('foto', 'produk')) {
            $this->forge->addColumn('produk', [
                'foto' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'keterangan'
                ],
            ]);
        }
    }

    public function down()
    {
        // Hapus kolom foto jika ada (saat rollback)
        if ($this->db->fieldExists('foto', 'produk')) {
            $this->forge->dropColumn('produk', 'foto');
        }
    }
}