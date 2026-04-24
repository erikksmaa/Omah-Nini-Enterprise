<?php
namespace App\Models;

use CodeIgniter\Model;

class PembelianModel extends Model
{
    protected $table = 'pembelian';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'no_invoice', 'id_supplier', 'id_user',
        'tanggal_pembelian', 'total_harga', 'catatan', 'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    // ========== VALIDATION RULES ==========
    protected $validationRules = [
        'no_invoice' => 'required|is_unique[pembelian.no_invoice]',
        'id_supplier' => 'required|is_not_unique[supplier.id]',
        'id_user' => 'required|is_not_unique[users.user_id]',
        'total_harga' => 'required|numeric|greater_than[0]'
    ];

    // ========== CUSTOM METHODS ==========

    /**
     * Get all pembelian with supplier info and pagination
     */
    public function getAllWithSupplier($perPage = 10)
    {
        return $this->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->orderBy('pembelian.id', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Get pembelian by ID with supplier info
     */
    public function getByIdWithSupplier($id)
    {
        return $this->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->find($id);
    }

    /**
     * Get pembelian for detail view with supplier and user info
     */
    public function getDetail($id)
    {
        $db = \Config\Database::connect();
        return $db->table('pembelian')
            ->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->where('pembelian.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Get total pembelian bulan ini
     */
    public function getTotalPembelianBulanIni()
    {
        return $this->selectSum('total_harga')
            ->where('MONTH(tanggal_pembelian)', date('m'))
            ->where('YEAR(tanggal_pembelian)', date('Y'))
            ->first()['total_harga'] ?? 0;
    }

    /**
     * Get count pembelian bulan ini
     */
    public function getCountPembelianBulanIni()
    {
        return $this->where('MONTH(tanggal_pembelian)', date('m'))
            ->where('YEAR(tanggal_pembelian)', date('Y'))
            ->countAllResults();
    }

    /**
     * Generate nomor invoice
     */
    public function generateNoInvoice()
    {
        $last = $this->orderBy('id', 'DESC')->first();
        if ($last && isset($last['no_invoice'])) {
            $lastNumber = (int) substr($last['no_invoice'], -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        return 'PO-' . date('ymd') . '-' . $newNumber;
    }

    /**
     * Save pembelian with transaction (insert manual via Query Builder)
     */
    public function savePembelian($data, $items, $userId)
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        
        $db->transStart();
        
        try {
            // Insert pembelian
            $db->table('pembelian')->insert($data);
            $pembelian_id = $db->insertID();
            
            foreach ($items as $item) {
                // Insert detail pembelian
                $detailData = [
                    'id_pembelian' => $pembelian_id,
                    'id_produk' => $item['id_produk'],
                    'nama_produk' => $item['nama_produk'],
                    'jumlah' => $item['jumlah'],
                    'harga_beli' => $item['harga_beli'],
                    'subtotal' => $item['subtotal']
                ];
                $db->table('detail_pembelian')->insert($detailData);
                
                // Update stok produk
                $produk = $db->table('produk')->where('id', $item['id_produk'])->get()->getRowArray();
                $stok_baru = $produk['stok'] + $item['jumlah'];
                $db->table('produk')->where('id', $item['id_produk'])->update([
                    'stok' => $stok_baru,
                    'harga_beli' => $item['harga_beli']
                ]);
                
                // Log stok
                $db->table('log_stok')->insert([
                    'id_produk' => $item['id_produk'],
                    'id_user' => $userId,
                    'tipe_ref' => 'pembelian',
                    'id_ref' => $pembelian_id,
                    'jumlah_sebelum' => $produk['stok'],
                    'jumlah_perubahan' => $item['jumlah'],
                    'jumlah_sesudah' => $stok_baru,
                    'aktivitas' => 'Pembelian barang',
                    'created_at' => $now
                ]);
            }
            
            // Insert keuangan
            $db->table('keuangan')->insert([
                'id_user' => $userId,
                'tipe' => 'pengeluaran',
                'kategori' => 'pembelian',
                'tipe_ref' => 'pembelian',
                'id_ref' => $pembelian_id,
                'jumlah' => $data['total_harga'],
                'tanggal_transaksi' => $data['tanggal_pembelian'],
                'created_at' => $now
            ]);
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal');
            }
            
            return ['success' => true, 'id' => $pembelian_id];
            
        } catch (\Exception $e) {
            $db->transRollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}