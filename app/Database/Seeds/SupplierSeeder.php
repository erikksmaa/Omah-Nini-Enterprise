<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama' => 'PT Mainan Kita Bersama',
                'kontak' => '081234567890',
                'email' => 'sales@mainankita.com',
                'alamat' => 'Jl. Raya Industri No. 123, Jakarta Barat',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama' => 'CV Toy Kingdom Indonesia',
                'kontak' => '081987654321',
                'email' => 'order@toykingdom.co.id',
                'alamat' => 'Jl. Mangga Dua Raya No. 45, Jakarta Utara',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama' => 'PT Happy Play Indonesia',
                'kontak' => '082112345678',
                'email' => 'cs@happyplay.com',
                'alamat' => 'Jl. Raya Serpong KM 12, Tangerang',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama' => 'UD Mainan Ceria',
                'kontak' => '083212345678',
                'email' => 'ceria@mainancepat.com',
                'alamat' => 'Jl. Ahmad Yani No. 78, Bandung',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama' => 'PT Edukasi Anak Bangsa',
                'kontak' => '085612345678',
                'email' => 'info@edukasianak.id',
                'alamat' => 'Jl. Pendidikan No. 9, Yogyakarta',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama' => 'CV Lego Importir',
                'kontak' => '087812345678',
                'email' => 'import@lego.id',
                'alamat' => 'Jl. Tanah Abang Timur No. 22, Jakarta Pusat',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama' => 'PT Robotik Indonesia',
                'kontak' => '088212345678',
                'email' => 'sales@robotik.id',
                'alamat' => 'Jl. Tekno Park No. 5, Bekasi',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'nama' => 'UD Boneka Lucu',
                'kontak' => '089612345678',
                'email' => 'lucu@boneka.com',
                'alamat' => 'Jl. Pasar Baru No. 15, Semarang',
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        $this->db->table('supplier')->insertBatch($data);
        
        echo "SupplierSeeder: " . count($data) . " supplier berhasil ditambahkan\n";
    }
}