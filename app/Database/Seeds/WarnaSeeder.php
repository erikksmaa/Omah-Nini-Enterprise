<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WarnaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['nama_warna' => 'Putih'],
            ['nama_warna' => 'Sogan'],
            ['nama_warna' => 'Abu'],
            ['nama_warna' => 'Biru'],
            ['nama_warna' => 'Mustard'],
            ['nama_warna' => 'Hijau'],
            ['nama_warna' => 'Toska'],
            ['nama_warna' => 'Merah'],
            ['nama_warna' => 'Hitam'],
            ['nama_warna' => 'Maroon'],
            ['nama_warna' => 'Navy'],
            ['nama_warna' => 'Kuning'],
            ['nama_warna' => 'Ungu'],
            ['nama_warna' => 'Orange'],
            ['nama_warna' => 'Coklat'],
            ['nama_warna' => 'Pink'],
            ['nama_warna' => 'Cream'],
            ['nama_warna' => 'Monocrom'],
        ];

        foreach ($data as $row) {
            $row['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('warna')->insert($row);
        }

        echo "✓ WarnaSeeder completed: " . count($data) . " records\n";
    }
}