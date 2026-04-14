<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
  // 1. Nama tabel harus sesuai dengan yang ada di migrasi/database
    protected $table            = 'produk'; 
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // 2. WAJIB DIISI: Daftar kolom yang boleh dimanipulasi (Insert/Update)
    protected $allowedFields    = [
        'kode_sku', 
        'barcode_ean', 
        'nama_produk', 
        'id_kategori', 
        'id_supplier', 
        'harga_beli', 
        'harga_jual', 
        'stok', 
        'safety_stock'
    ];

    // 3. Aktifkan Timestamps agar created_at & updated_at terisi otomatis
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // 4. Tambahkan fungsi Enterprise untuk logika stok
    public function kurangiStok($id, $jumlah)
    {
        $produk = $this->find($id);
        if (!$produk || $produk['stok'] < $jumlah) {
            return false; 
        }
        return $this->update($id, ['stok' => $produk['stok'] - $jumlah]);
    }
}
