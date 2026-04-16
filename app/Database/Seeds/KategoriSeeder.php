<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['nama' => 'Action Figure', 'deskripsi' => 'Mainan karakter superhero, anime, dan film populer', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Mobil-mobilan', 'deskripsi' => 'Mainan mobil, truk, dan kendaraan lainnya', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Boneka', 'deskripsi' => 'Boneka beruang, boneka karakter, dan soft toy', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Lego & Blok', 'deskripsi' => 'Lego, blok bangunan, dan mainan konstruksi', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Board Game', 'deskripsi' => 'Permainan papan dan kartu untuk keluarga', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Robot & Elektronik', 'deskripsi' => 'Mainan robot, drone, dan mainan elektronik', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Mainan Edukasi', 'deskripsi' => 'Mainan untuk belajar dan mengasah otak', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Outdoor & Olahraga', 'deskripsi' => 'Mainan untuk bermain di luar ruangan', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Puzzle', 'deskripsi' => 'Puzzle berbagai ukuran dan tingkat kesulitan', 'created_at' => date('Y-m-d H:i:s')],
            ['nama' => 'Mainan Bayi', 'deskripsi' => 'Mainan untuk bayi 0-3 tahun', 'created_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('kategori')->insertBatch($data);
        
        echo "KategoriSeeder: " . count($data) . " kategori berhasil ditambahkan\n";
    }
}