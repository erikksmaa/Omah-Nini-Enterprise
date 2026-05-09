<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHargaToDetailTransaksi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('detail_transaksi', [
            'harga_satuan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'after'      => 'jumlah',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('detail_transaksi', 'harga_satuan');
    }
}