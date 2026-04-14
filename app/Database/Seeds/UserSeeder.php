<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder // Harus sama dengan nama file
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin',
                'password' => password_hash('123', PASSWORD_BCRYPT),
                'role'     => 'admin', // Sesuai desain A3 
            ],
            [
                'username' => 'gudang',
                'password' => password_hash('123', PASSWORD_BCRYPT),
                'role'     => 'gudang', // Sesuai desain A3 
            ],
            [
                'username' => 'kasir',
                'password' => password_hash('123', PASSWORD_BCRYPT),
                'role'     => 'kasir', // Sesuai desain A3 
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}