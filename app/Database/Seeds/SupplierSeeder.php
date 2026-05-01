<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama'       => 'Bang Jack\'s / Aulia',
                'kontak'     => '081234567890',
                'email'      => 'bangjacks@example.com',
                'alamat'     => 'Jl. Batik No. 45, Pekalongan',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama'       => 'Maida Exclusive',
                'kontak'     => '081234567891',
                'email'      => 'maida@example.com',
                'alamat'     => 'Jl. Raya Batik No. 12, Solo',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama'       => 'Maida Katun Super',
                'kontak'     => '081234567892',
                'email'      => 'maida.katun@example.com',
                'alamat'     => 'Jl. Raya Batik No. 12, Solo',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama'       => 'AL FATI',
                'kontak'     => '081234567893',
                'email'      => 'alfati@example.com',
                'alamat'     => 'Jl. Batik KM 5, Yogyakarta',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($data as $row) {
            $this->db->table('supplier')->insert($row);
        }

        echo "✓ SupplierSeeder completed\n";
    }
}