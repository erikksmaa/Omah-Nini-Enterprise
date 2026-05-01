<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
         // Urutan penting karena foreign key
        $this->call('UserSeeder');
        $this->call('SupplierSeeder');
        $this->call('WarnaSeeder');
        $this->call('MotifSeeder');
        $this->call('ProdukSeeder');
        $this->call('PelangganSeeder');

        echo "\n✅ ALL SEEDERS COMPLETED!\n";
    }
}
