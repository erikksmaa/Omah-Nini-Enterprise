<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanKasirModel extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'no_invoice', 'id_user', 'tanggal_transaksi', 
        'total_bayar', 'tipe_pembayaran', 'status', 'catatan', 'created_at'
    ];
    
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = null;
    
    protected $db;
    protected $produkModel;
    protected $transaksiModel;
    protected $detailTransaksiModel;
    protected $logStokModel;
    protected $keuanganModel;
    
    // Batas waktu pembatalan dalam menit (default: 60 menit)
    protected $cancelTimeLimit = 60;
    
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->produkModel = new ProdukModel();
        $this->transaksiModel = new TransaksiModel();
        $this->detailTransaksiModel = new DetailTransaksiModel();
        $this->logStokModel = new LogStokModel();
        $this->keuanganModel = new KeuanganModel();
    }
    
    /**
     * Cek apakah transaksi masih bisa dibatalkan
     * @param int $id ID transaksi
     * @return array ['can_cancel' => bool, 'remaining_minutes' => int, 'message' => string]
     */
    public function canCancelTransaction($id)
    {
        $transaksi = $this->find($id);
        if (!$transaksi) {
            return [
                'can_cancel' => false,
                'remaining_minutes' => 0,
                'message' => 'Transaksi tidak ditemukan'
            ];
        }
        
        if ($transaksi['status'] == 'batal') {
            return [
                'can_cancel' => false,
                'remaining_minutes' => 0,
                'message' => 'Transaksi sudah dibatalkan sebelumnya'
            ];
        }
        
        if ($transaksi['status'] != 'selesai') {
            return [
                'can_cancel' => false,
                'remaining_minutes' => 0,
                'message' => 'Transaksi tidak dapat dibatalkan'
            ];
        }
        
        $createdAt = strtotime($transaksi['created_at']);
        $now = time();
        $timeDiff = ($now - $createdAt) / 60; // selisih dalam menit
        $remainingMinutes = max(0, $this->cancelTimeLimit - $timeDiff);
        $canCancel = ($timeDiff <= $this->cancelTimeLimit);
        
        if (!$canCancel) {
            return [
                'can_cancel' => false,
                'remaining_minutes' => 0,
                'message' => 'Transaksi sudah melewati batas waktu pembatalan (' . $this->cancelTimeLimit . ' menit)'
            ];
        }
        
        return [
            'can_cancel' => true,
            'remaining_minutes' => round($remainingMinutes),
            'message' => 'Masih dalam batas waktu pembatalan'
        ];
    }
    
    /**
     * Format sisa waktu menjadi string
     */
    public function formatRemainingTime($minutes)
    {
        if ($minutes <= 0) return 'Batas waktu habis';
        
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0) {
            return $hours . ' jam ' . $mins . ' menit';
        }
        return $mins . ' menit';
    }
    
    /**
     * Cancel transaksi with time limit check
     */
    public function cancelPenjualanWithTimeLimit($id, $userId, $isAdmin = false)
    {
        // Cek apakah bisa dibatalkan
        $check = $this->canCancelTransaction($id);
        
        // Admin bisa membatalkan kapan saja (opsional)
        if (!$isAdmin && !$check['can_cancel']) {
            return ['success' => false, 'error' => $check['message']];
        }
        
        $transaksi = $this->find($id);
        
        $this->db->transStart();
        
        try {
            $detail = $this->detailTransaksiModel->where('id_transaksi', $id)->findAll();
            
            foreach ($detail as $item) {
                // Kembalikan stok
                $produk = $this->produkModel->find($item['id_produk']);
                $stok_sesudah = $produk['stok'] + $item['jumlah'];
                $this->produkModel->update($item['id_produk'], ['stok' => $stok_sesudah]);
                
                // Log stok pembatalan
                $createdAt = strtotime($transaksi['created_at']);
                $now = time();
                $timeDiff = ($now - $createdAt) / 60;
                $isLate = ($timeDiff > $this->cancelTimeLimit);
                
                $this->logStokModel->insert([
                    'id_produk' => $item['id_produk'],
                    'id_user' => $userId,
                    'tipe_ref' => 'penjualan_batal',
                    'id_ref' => $id,
                    'jumlah_sebelum' => $produk['stok'],
                    'jumlah_perubahan' => $item['jumlah'],
                    'jumlah_sesudah' => $stok_sesudah,
                    'aktivitas' => 'Pembatalan transaksi penjualan' . ($isLate ? ' (melebihi batas waktu - dibatalkan oleh admin)' : ''),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            // Update status transaksi
            $this->update($id, ['status' => 'batal']);
            
            // Hapus catatan keuangan
            $this->keuanganModel->where('tipe_ref', 'penjualan')->where('id_ref', $id)->delete();
            
            $this->db->transComplete();
            
            $message = $check['can_cancel'] 
                ? 'Transaksi berhasil dibatalkan' 
                : 'Transaksi berhasil dibatalkan oleh admin (melebihi batas waktu)';
            
            return ['success' => true, 'message' => $message];
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
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
        return 'INV-' . date('ymd') . '-' . $newNumber;
    }
    
    /**
     * Get all transaksi for index with pagination and search
     */
    public function getAllTransaksi($search = null, $perPage = 10, $page = 1)
    {
        $offset = ($page - 1) * $perPage;
        
        $builder = $this->orderBy('id', 'DESC');
        
        if (!empty($search)) {
            $builder->like('no_invoice', $search);
        }
        
        $total = $builder->countAllResults(false);
        $data = $builder->limit($perPage, $offset)->findAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page
        ];
    }
    
    /**
     * Get produk with stok > 0 for POS
     */
    public function getAvailableProducts()
    {
        return $this->produkModel->where('stok >', 0)->findAll();
    }
    
    /**
     * Search produk by keyword
     */
    public function searchProducts($keyword)
    {
        return $this->produkModel->groupStart()
            ->like('nama_barang', $keyword)
            ->orLike('sku', $keyword)
            ->groupEnd()
            ->where('stok >', 0)
            ->findAll();
    }
    
    /**
     * Save transaksi penjualan with all related data
     */
    public function savePenjualan($items, $totalBelanja, $bayar, $tipePembayaran, $catatan, $userId)
    {
        $now = date('Y-m-d H:i:s');
        $no_invoice = $this->generateNoInvoice();
        
        $this->db->transStart();
        
        try {
            // Insert transaksi
            $transaksiData = [
                'no_invoice' => $no_invoice,
                'id_user' => $userId,
                'tanggal_transaksi' => $now,
                'total_bayar' => $bayar,
                'tipe_pembayaran' => $tipePembayaran,
                'status' => 'selesai',
                'catatan' => $catatan,
                'created_at' => $now
            ];
            $this->insert($transaksiData);
            $transaksi_id = $this->getInsertID();
            
            foreach ($items as $item) {
                // Insert detail transaksi
                $this->detailTransaksiModel->insert([
                    'id_transaksi' => $transaksi_id,
                    'id_produk' => $item['id_produk'],
                    'nama_produk' => $item['nama_produk'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_jual'],
                    'subtotal' => $item['subtotal']
                ]);
                
                // Update stok
                $produk = $this->produkModel->find($item['id_produk']);
                $stok_baru = $produk['stok'] - $item['jumlah'];
                $this->produkModel->update($item['id_produk'], ['stok' => $stok_baru]);
                
                // Log stok
                $this->logStokModel->insert([
                    'id_produk' => $item['id_produk'],
                    'id_user' => $userId,
                    'tipe_ref' => 'penjualan',
                    'id_ref' => $transaksi_id,
                    'jumlah_sebelum' => $produk['stok'],
                    'jumlah_perubahan' => -$item['jumlah'],
                    'jumlah_sesudah' => $stok_baru,
                    'aktivitas' => 'Penjualan ke customer',
                    'created_at' => $now
                ]);
            }
            
            // Insert keuangan
            $this->keuanganModel->insert([
                'id_user' => $userId,
                'tipe' => 'pemasukan',
                'kategori' => 'penjualan',
                'tipe_ref' => 'penjualan',
                'id_ref' => $transaksi_id,
                'jumlah' => $totalBelanja,
                'tanggal_transaksi' => date('Y-m-d'),
                'created_at' => $now
            ]);
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi gagal');
            }
            
            // Get data for struk
            $transaksi = $this->find($transaksi_id);
            $detail = $this->detailTransaksiModel->where('id_transaksi', $transaksi_id)->findAll();
            
            return [
                'success' => true,
                'transaksi' => $transaksi,
                'detail' => $detail,
                'transaksi_id' => $transaksi_id
            ];
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Get transaksi detail for struk
     */
    public function getStrukDetail($id)
    {
        $transaksi = $this->find($id);
        if (!$transaksi) {
            return null;
        }
        
        $detail = $this->detailTransaksiModel->where('id_transaksi', $id)->findAll();
        
        $total_belanja = 0;
        foreach ($detail as $item) {
            $total_belanja += $item['subtotal'];
        }
        
        return [
            'transaksi' => $transaksi,
            'detail' => $detail,
            'total_belanja' => $total_belanja,
            'kembalian' => $transaksi['total_bayar'] - $total_belanja
        ];
    }
    
    
    /**
     * Get struk data for AJAX
     */
    public function getStrukData($id)
    {
        $transaksi = $this->find($id);
        if (!$transaksi) {
            return null;
        }
        
        $detail = $this->detailTransaksiModel->where('id_transaksi', $id)->findAll();
        
        $total_belanja = 0;
        foreach ($detail as $item) {
            $total_belanja += $item['subtotal'];
        }
        
        return [
            'transaksi' => $transaksi,
            'detail' => $detail,
            'total_belanja' => $total_belanja,
            'kembalian' => $transaksi['total_bayar'] - $total_belanja,
            'kasir' => session()->get('username')
        ];
    }
}