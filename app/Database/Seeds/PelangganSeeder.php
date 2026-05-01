<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PelangganSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama'       => 'Husna',
                'alamat'     => 'Jl. Merdeka No. 10, Pekalongan',
                'no_telp'    => '081234567001',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama'       => 'Wahyu',
                'alamat'     => 'Jl. Diponegoro No. 25, Pekalongan',
                'no_telp'    => '081234567002',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama'       => 'Siti',
                'alamat'     => 'Jl. Batik No. 7, Solo',
                'no_telp'    => '081234567003',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama'       => 'Budi',
                'alamat'     => 'Jl. Raya No. 45, Yogyakarta',
                'no_telp'    => '081234567004',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($data as $row) {
            $this->db->table('pelanggan')->insert($row);
        }

        echo "✓ PelangganSeeder completed\n";
    }
}