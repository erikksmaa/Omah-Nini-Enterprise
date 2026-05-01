<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MotifSeeder extends Seeder
{
    public function run()
    {
        // Ambil ID supplier
        $suppliers = $this->db->table('supplier')->get()->getResultArray();
        $supplierMap = [];
        foreach ($suppliers as $s) {
            $supplierMap[$s['nama']] = $s['id'];
        }

        $data = [
            // Bang Jack's / Aulia
            ['id_supplier' => $supplierMap['Bang Jack\'s / Aulia'], 'nama_motif' => 'Sekar Jagat Laseman Series'],
            ['id_supplier' => $supplierMap['Bang Jack\'s / Aulia'], 'nama_motif' => 'Bakaran Putih'],
            ['id_supplier' => $supplierMap['Bang Jack\'s / Aulia'], 'nama_motif' => 'Cendrawasih'],
            ['id_supplier' => $supplierMap['Bang Jack\'s / Aulia'], 'nama_motif' => 'Jlamprang Tantum'],
            ['id_supplier' => $supplierMap['Bang Jack\'s / Aulia'], 'nama_motif' => 'Blarak Lantang'],
            ['id_supplier' => $supplierMap['Bang Jack\'s / Aulia'], 'nama_motif' => 'Bakaran Pagi Sore'],
            
            // Maida Exclusive
            ['id_supplier' => $supplierMap['Maida Exclusive'], 'nama_motif' => 'Maida Exclusive 01'],
            ['id_supplier' => $supplierMap['Maida Exclusive'], 'nama_motif' => 'Maida Exclusive 02'],
            ['id_supplier' => $supplierMap['Maida Exclusive'], 'nama_motif' => 'Maida Exclusive 03'],
            ['id_supplier' => $supplierMap['Maida Exclusive'], 'nama_motif' => 'Maida Exclusive 04'],
            ['id_supplier' => $supplierMap['Maida Exclusive'], 'nama_motif' => 'Maida Exclusive 05'],
            ['id_supplier' => $supplierMap['Maida Exclusive'], 'nama_motif' => 'Maida Exclusive 06'],
            ['id_supplier' => $supplierMap['Maida Exclusive'], 'nama_motif' => 'Maida Exclusive 07'],
            ['id_supplier' => $supplierMap['Maida Exclusive'], 'nama_motif' => 'Maida Exclusive 08'],
            
            // Maida Katun Super
            ['id_supplier' => $supplierMap['Maida Katun Super'], 'nama_motif' => 'Maida Katun Super 01'],
            ['id_supplier' => $supplierMap['Maida Katun Super'], 'nama_motif' => 'Maida Katun Super 02'],
            ['id_supplier' => $supplierMap['Maida Katun Super'], 'nama_motif' => 'Maida Katun Super 03'],
            ['id_supplier' => $supplierMap['Maida Katun Super'], 'nama_motif' => 'Maida Katun Super 04'],
            ['id_supplier' => $supplierMap['Maida Katun Super'], 'nama_motif' => 'Maida Katun Super 05'],
            ['id_supplier' => $supplierMap['Maida Katun Super'], 'nama_motif' => 'Maida Katun Super 06'],
            ['id_supplier' => $supplierMap['Maida Katun Super'], 'nama_motif' => 'Maida Katun Super 07'],
            ['id_supplier' => $supplierMap['Maida Katun Super'], 'nama_motif' => 'Maida Katun Super 08'],
            
            // AL FATI
            ['id_supplier' => $supplierMap['AL FATI'], 'nama_motif' => 'Wayans'],
            ['id_supplier' => $supplierMap['AL FATI'], 'nama_motif' => 'Siyomakti'],
            ['id_supplier' => $supplierMap['AL FATI'], 'nama_motif' => 'Litis 09'],
            ['id_supplier' => $supplierMap['AL FATI'], 'nama_motif' => 'Seno Repekhan'],
            ['id_supplier' => $supplierMap['AL FATI'], 'nama_motif' => 'Benowd'],
            ['id_supplier' => $supplierMap['AL FATI'], 'nama_motif' => 'Lagonian Nadhiza'],
            ['id_supplier' => $supplierMap['AL FATI'], 'nama_motif' => 'Kalmaghan'],
            ['id_supplier' => $supplierMap['AL FATI'], 'nama_motif' => 'Macon'],
        ];

        foreach ($data as $row) {
            $row['created_at'] = date('Y-m-d H:i:s');
            $row['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('motif')->insert($row);
        }

        echo "✓ MotifSeeder completed\n";
    }
}