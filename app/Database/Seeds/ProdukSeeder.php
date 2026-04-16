<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run()
    {
        // Ambil ID kategori dan supplier yang sudah ada
        $kategori = $this->db->table('kategori')->select('id, nama')->get()->getResultArray();
        $supplier = $this->db->table('supplier')->select('id, nama')->get()->getResultArray();
        
        // Buat mapping nama ke ID
        $kategoriMap = [];
        foreach ($kategori as $kat) {
            $kategoriMap[$kat['nama']] = $kat['id'];
        }
        
        $supplierMap = [];
        foreach ($supplier as $sup) {
            $supplierMap[$sup['nama']] = $sup['id'];
        }
        
        // Cek apakah data sudah ada
        if (empty($kategoriMap) || empty($supplierMap)) {
            echo "Error: Jalankan KategoriSeeder dan SupplierSeeder terlebih dahulu!\n";
            return;
        }
        
        $produk = [
            // Action Figure
            ['sku' => 'AF-001', 'nama_barang' => 'Action Figure Iron Man Mark 85', 
             'id_kategori' => $kategoriMap['Action Figure'], 'id_supplier' => $supplierMap['PT Mainan Kita Bersama'], 
             'harga_beli' => 350000, 'harga_jual' => 450000, 'stok' => 25, 'min_stok' => 10, 
             'keterangan' => 'Action figure Iron Man dari Avengers Endgame, high quality'],
             
            ['sku' => 'AF-002', 'nama_barang' => 'Action Figure Spider-Man Miles Morales', 
             'id_kategori' => $kategoriMap['Action Figure'], 'id_supplier' => $supplierMap['PT Mainan Kita Bersama'], 
             'harga_beli' => 280000, 'harga_jual' => 380000, 'stok' => 30, 'min_stok' => 10, 
             'keterangan' => 'Action figure Spider-Man versi Miles Morales'],
             
            ['sku' => 'AF-003', 'nama_barang' => 'Action Figure Naruto Sage Mode', 
             'id_kategori' => $kategoriMap['Action Figure'], 'id_supplier' => $supplierMap['CV Toy Kingdom Indonesia'], 
             'harga_beli' => 220000, 'harga_jual' => 320000, 'stok' => 15, 'min_stok' => 5, 
             'keterangan' => 'Action figure Naruto mode sage, bisa diganti muka'],
             
            ['sku' => 'AF-004', 'nama_barang' => 'Action Figure Goku Ultra Instinct', 
             'id_kategori' => $kategoriMap['Action Figure'], 'id_supplier' => $supplierMap['CV Toy Kingdom Indonesia'], 
             'harga_beli' => 250000, 'harga_jual' => 350000, 'stok' => 20, 'min_stok' => 8, 
             'keterangan' => 'Action figure Goku dengan rambut silver'],
            
            // Mobil-mobilan
            ['sku' => 'MC-001', 'nama_barang' => 'Hot Wheels Mobil Balap', 
             'id_kategori' => $kategoriMap['Mobil-mobilan'], 'id_supplier' => $supplierMap['PT Happy Play Indonesia'], 
             'harga_beli' => 35000, 'harga_jual' => 55000, 'stok' => 100, 'min_stok' => 30, 
             'keterangan' => 'Hot Wheels original, berbagai model'],
             
            ['sku' => 'MC-002', 'nama_barang' => 'RC Mobil Remote Control', 
             'id_kategori' => $kategoriMap['Mobil-mobilan'], 'id_supplier' => $supplierMap['PT Happy Play Indonesia'], 
             'harga_beli' => 180000, 'harga_jual' => 280000, 'stok' => 12, 'min_stok' => 5, 
             'keterangan' => 'Mobil remote control dengan baterai rechargeable'],
             
            ['sku' => 'MC-003', 'nama_barang' => 'Truk Molen Mainan', 
             'id_kategori' => $kategoriMap['Mobil-mobilan'], 'id_supplier' => $supplierMap['UD Mainan Ceria'], 
             'harga_beli' => 75000, 'harga_jual' => 120000, 'stok' => 20, 'min_stok' => 10, 
             'keterangan' => 'Truk molen dengan putaran beton'],
             
            ['sku' => 'MC-004', 'nama_barang' => 'Kereta Api Mainan', 
             'id_kategori' => $kategoriMap['Mobil-mobilan'], 'id_supplier' => $supplierMap['UD Mainan Ceria'], 
             'harga_beli' => 150000, 'harga_jual' => 220000, 'stok' => 8, 'min_stok' => 3, 
             'keterangan' => 'Set kereta api dengan rel 2 meter'],
            
            // Boneka
            ['sku' => 'BK-001', 'nama_barang' => 'Boneka Teddy Bear Besar', 
             'id_kategori' => $kategoriMap['Boneka'], 'id_supplier' => $supplierMap['UD Boneka Lucu'], 
             'harga_beli' => 120000, 'harga_jual' => 200000, 'stok' => 15, 'min_stok' => 5, 
             'keterangan' => 'Boneka beruang ukuran 70cm, sangat lembut'],
             
            ['sku' => 'BK-002', 'nama_barang' => 'Boneka Barbie Fashion', 
             'id_kategori' => $kategoriMap['Boneka'], 'id_supplier' => $supplierMap['UD Boneka Lucu'], 
             'harga_beli' => 90000, 'harga_jual' => 150000, 'stok' => 25, 'min_stok' => 10, 
             'keterangan' => 'Boneka Barbie dengan pakaian fashion terbaru'],
             
            ['sku' => 'BK-003', 'nama_barang' => 'Boneka Karakter Disney', 
             'id_kategori' => $kategoriMap['Boneka'], 'id_supplier' => $supplierMap['CV Toy Kingdom Indonesia'], 
             'harga_beli' => 85000, 'harga_jual' => 135000, 'stok' => 18, 'min_stok' => 6, 
             'keterangan' => 'Boneka karakter Disney original'],
            
            // Lego & Blok
            ['sku' => 'LG-001', 'nama_barang' => 'Lego Classic 1000 pcs', 
             'id_kategori' => $kategoriMap['Lego & Blok'], 'id_supplier' => $supplierMap['CV Lego Importir'], 
             'harga_beli' => 250000, 'harga_jual' => 380000, 'stok' => 10, 'min_stok' => 4, 
             'keterangan' => 'Lego classic 1000 pieces, berbagai warna'],
             
            ['sku' => 'LG-002', 'nama_barang' => 'Blok Bangunan Castle', 
             'id_kategori' => $kategoriMap['Lego & Blok'], 'id_supplier' => $supplierMap['CV Lego Importir'], 
             'harga_beli' => 180000, 'harga_jual' => 280000, 'stok' => 7, 'min_stok' => 3, 
             'keterangan' => 'Set blok bangunan bentuk kastil'],
             
            ['sku' => 'LG-003', 'nama_barang' => 'Lego Technic Motor', 
             'id_kategori' => $kategoriMap['Lego & Blok'], 'id_supplier' => $supplierMap['CV Lego Importir'], 
             'harga_beli' => 320000, 'harga_jual' => 480000, 'stok' => 5, 'min_stok' => 2, 
             'keterangan' => 'Lego technic dengan sistem gear dan motor'],
            
            // Board Game
            ['sku' => 'BG-001', 'nama_barang' => 'Ular Tangga Raksasa', 
             'id_kategori' => $kategoriMap['Board Game'], 'id_supplier' => $supplierMap['PT Edukasi Anak Bangsa'], 
             'harga_beli' => 65000, 'harga_jual' => 100000, 'stok' => 20, 'min_stok' => 8, 
             'keterangan' => 'Ular tangga ukuran besar untuk keluarga'],
             
            ['sku' => 'BG-002', 'nama_barang' => 'Monopoly Classic', 
             'id_kategori' => $kategoriMap['Board Game'], 'id_supplier' => $supplierMap['PT Edukasi Anak Bangsa'], 
             'harga_beli' => 180000, 'harga_jual' => 280000, 'stok' => 12, 'min_stok' => 5, 
             'keterangan' => 'Permainan monopoly classic original'],
             
            ['sku' => 'BG-003', 'nama_barang' => 'Scrabble Bahasa Indonesia', 
             'id_kategori' => $kategoriMap['Board Game'], 'id_supplier' => $supplierMap['PT Edukasi Anak Bangsa'], 
             'harga_beli' => 150000, 'harga_jual' => 230000, 'stok' => 8, 'min_stok' => 3, 
             'keterangan' => 'Permainan scrabble edisi bahasa indonesia'],
            
            // Robot & Elektronik
            ['sku' => 'RB-001', 'nama_barang' => 'Robot Dancing', 
             'id_kategori' => $kategoriMap['Robot & Elektronik'], 'id_supplier' => $supplierMap['PT Robotik Indonesia'], 
             'harga_beli' => 200000, 'harga_jual' => 320000, 'stok' => 6, 'min_stok' => 2, 
             'keterangan' => 'Robot bisa menari dan menyala'],
             
            ['sku' => 'RB-002', 'nama_barang' => 'Drone Mini', 
             'id_kategori' => $kategoriMap['Robot & Elektronik'], 'id_supplier' => $supplierMap['PT Robotik Indonesia'], 
             'harga_beli' => 350000, 'harga_jual' => 500000, 'stok' => 4, 'min_stok' => 2, 
             'keterangan' => 'Drone mini dengan kamera HD'],
            
            // Mainan Edukasi
            ['sku' => 'ED-001', 'nama_barang' => 'Flashcard Anak', 
             'id_kategori' => $kategoriMap['Mainan Edukasi'], 'id_supplier' => $supplierMap['PT Edukasi Anak Bangsa'], 
             'harga_beli' => 45000, 'harga_jual' => 75000, 'stok' => 40, 'min_stok' => 15, 
             'keterangan' => 'Kartu belajar untuk balita'],
             
            ['sku' => 'ED-002', 'nama_barang' => 'Papan Tulis Magnetik', 
             'id_kategori' => $kategoriMap['Mainan Edukasi'], 'id_supplier' => $supplierMap['PT Edukasi Anak Bangsa'], 
             'harga_beli' => 85000, 'harga_jual' => 135000, 'stok' => 15, 'min_stok' => 5, 
             'keterangan' => 'Papan tulis 2 sisi dengan magnet'],
            
            // Outdoor & Olahraga
            ['sku' => 'OT-001', 'nama_barang' => 'Sepatu Roda Anak', 
             'id_kategori' => $kategoriMap['Outdoor & Olahraga'], 'id_supplier' => $supplierMap['UD Mainan Ceria'], 
             'harga_beli' => 180000, 'harga_jual' => 280000, 'stok' => 10, 'min_stok' => 4, 
             'keterangan' => 'Sepatu roda dengan ukuran adjustable'],
             
            ['sku' => 'OT-002', 'nama_barang' => 'Layang-layang Hias', 
             'id_kategori' => $kategoriMap['Outdoor & Olahraga'], 'id_supplier' => $supplierMap['UD Mainan Ceria'], 
             'harga_beli' => 25000, 'harga_jual' => 45000, 'stok' => 50, 'min_stok' => 20, 
             'keterangan' => 'Layang-layang dengan gambar naga'],
            
            // Puzzle
            ['sku' => 'PZ-001', 'nama_barang' => 'Puzzle 500 pcs', 
             'id_kategori' => $kategoriMap['Puzzle'], 'id_supplier' => $supplierMap['PT Mainan Kita Bersama'], 
             'harga_beli' => 75000, 'harga_jual' => 120000, 'stok' => 12, 'min_stok' => 5, 
             'keterangan' => 'Puzzle 500 pcs gambar pemandangan'],
             
            ['sku' => 'PZ-002', 'nama_barang' => 'Puzzle 3D', 
             'id_kategori' => $kategoriMap['Puzzle'], 'id_supplier' => $supplierMap['PT Mainan Kita Bersama'], 
             'harga_beli' => 95000, 'harga_jual' => 150000, 'stok' => 8, 'min_stok' => 3, 
             'keterangan' => 'Puzzle 3D bentuk bangunan terkenal'],
            
            // Mainan Bayi
            ['sku' => 'BY-001', 'nama_barang' => 'Rattle Bayi', 
             'id_kategori' => $kategoriMap['Mainan Bayi'], 'id_supplier' => $supplierMap['UD Boneka Lucu'], 
             'harga_beli' => 25000, 'harga_jual' => 45000, 'stok' => 60, 'min_stok' => 20, 
             'keterangan' => 'Mainan gemerincing untuk bayi'],
             
            ['sku' => 'BY-002', 'nama_barang' => 'Playmat Bayi', 
             'id_kategori' => $kategoriMap['Mainan Bayi'], 'id_supplier' => $supplierMap['UD Boneka Lucu'], 
             'harga_beli' => 120000, 'harga_jual' => 200000, 'stok' => 10, 'min_stok' => 4, 
             'keterangan' => 'Alas bermain bayi dengan gantungan'],
        ];

        // Insert dalam batch
        $chunks = array_chunk($produk, 10);
        foreach ($chunks as $chunk) {
            $this->db->table('produk')->insertBatch($chunk);
        }
        
        echo "ProdukSeeder: " . count($produk) . " produk berhasil ditambahkan\n";
    }
}