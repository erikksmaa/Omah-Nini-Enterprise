<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProdukTable extends Migration
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
            'sku' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_barang' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'id_kategori' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_supplier' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'harga_beli' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'harga_jual' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'stok' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'min_stok' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
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
        $this->forge->addUniqueKey('sku');
        $this->forge->addForeignKey('id_kategori', 'kategori', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('id_supplier', 'supplier', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('produk');
    }

    public function down()
    {
        $this->forge->dropTable('produk');
    }
}