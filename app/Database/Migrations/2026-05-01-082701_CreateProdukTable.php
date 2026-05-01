<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProdukTable extends Migration
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
            'sku' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'id_supplier' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'id_motif' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'id_warna' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'stok' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'min_stok' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_supplier', 'supplier', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('id_motif', 'motif', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('id_warna', 'warna', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('produk');
    }

    public function down()
    {
        $this->forge->dropTable('produk');
    }
}