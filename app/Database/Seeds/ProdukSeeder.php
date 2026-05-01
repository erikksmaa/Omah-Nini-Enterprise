<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run()
    {
        // Ambil data reference
        $suppliers = $this->db->table('supplier')->get()->getResultArray();
        $motifs = $this->db->table('motif')->get()->getResultArray();
        $warnas = $this->db->table('warna')->get()->getResultArray();

        // Buat mapping
        $supplierMap = [];
        foreach ($suppliers as $s) {
            $supplierMap[$s['nama']] = $s['id'];
        }

        $motifMap = [];
        foreach ($motifs as $m) {
            $key = $m['id_supplier'] . '|' . $m['nama_motif'];
            $motifMap[$key] = $m['id'];
        }

        $warnaMap = [];
        foreach ($warnas as $w) {
            $warnaMap[$w['nama_warna']] = $w['id'];
        }

        $data = [];

        // Produk contoh untuk demo
        $produkList = [
            ['supplier' => 'Bang Jack\'s / Aulia', 'motif' => 'Sekar Jagat Laseman Series', 'warna' => 'Abu', 'stok' => 72, 'min_stok' => 10],
            ['supplier' => 'Bang Jack\'s / Aulia', 'motif' => 'Sekar Jagat Laseman Series', 'warna' => 'Biru', 'stok' => 45, 'min_stok' => 10],
            ['supplier' => 'Bang Jack\'s / Aulia', 'motif' => 'Cendrawasih', 'warna' => 'Biru', 'stok' => 77, 'min_stok' => 10],
            ['supplier' => 'Bang Jack\'s / Aulia', 'motif' => 'Cendrawasih', 'warna' => 'Mustard', 'stok' => 85, 'min_stok' => 10],
            ['supplier' => 'Bang Jack\'s / Aulia', 'motif' => 'Jlamprang Tantum', 'warna' => 'Hijau', 'stok' => 5, 'min_stok' => 5],
            ['supplier' => 'Maida Exclusive', 'motif' => 'Maida Exclusive 01', 'warna' => 'Putih', 'stok' => 12, 'min_stok' => 5],
            ['supplier' => 'Maida Exclusive', 'motif' => 'Maida Exclusive 02', 'warna' => 'Putih', 'stok' => 11, 'min_stok' => 5],
            ['supplier' => 'Maida Katun Super', 'motif' => 'Maida Katun Super 01', 'warna' => 'Putih', 'stok' => 12, 'min_stok' => 5],
            ['supplier' => 'AL FATI', 'motif' => 'Wayans', 'warna' => 'Sogan', 'stok' => 50, 'min_stok' => 10],
            ['supplier' => 'AL FATI', 'motif' => 'Litis 09', 'warna' => 'Toska', 'stok' => 63, 'min_stok' => 10],
        ];

        $counter = 1;
        foreach ($produkList as $item) {
            $supplierId = $supplierMap[$item['supplier']];
            $motifKey = $supplierId . '|' . $item['motif'];
            $motifId = $motifMap[$motifKey] ?? null;
            $warnaId = $warnaMap[$item['warna']] ?? null;

            if ($motifId && $warnaId) {
                $supplierCode = $this->getSupplierCode($item['supplier']);
                $motifCode = $this->generateCode($item['motif'], 3);
                $warnaCode = $this->generateCode($item['warna'], 3);
                $sku = $supplierCode . '-' . $motifCode . '-' . $warnaCode . '-' . str_pad($counter++, 3, '0', STR_PAD_LEFT);

                $data[] = [
                    'sku'         => $sku,
                    'id_supplier' => $supplierId,
                    'id_motif'    => $motifId,
                    'id_warna'    => $warnaId,
                    'stok'        => $item['stok'],
                    'min_stok'    => $item['min_stok'],
                    'keterangan'  => $item['motif'] . ' - ' . $item['warna'],
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];
            }
        }

        if (!empty($data)) {
            foreach ($data as $row) {
                $this->db->table('produk')->insert($row);
            }
            echo "✓ ProdukSeeder completed: " . count($data) . " records\n";
        } else {
            echo "⚠ ProdukSeeder: No data inserted\n";
        }
    }

    private function getSupplierCode($nama)
    {
        $codes = [
            'Bang Jack\'s / Aulia' => 'BJK',
            'Maida Exclusive' => 'MDX',
            'Maida Katun Super' => 'MDK',
            'AL FATI' => 'AFT'
        ];
        return $codes[$nama] ?? substr(preg_replace('/[^A-Z]/', '', strtoupper($nama)), 0, 3);
    }

    private function generateCode($text, $length)
    {
        $clean = preg_replace('/[^A-Za-z0-9]/', '', $text);
        $code = strtoupper(substr($clean, 0, $length));
        if (strlen($code) < $length) {
            $code = str_pad($code, $length, 'X');
        }
        return $code;
    }
}