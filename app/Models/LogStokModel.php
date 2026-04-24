<?php

namespace App\Models;

use CodeIgniter\Model;

class LogStokModel extends Model
{
    protected $table = 'log_stok';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'id_produk', 'id_user', 'tipe_ref', 'id_ref',
        'jumlah_sebelum', 'jumlah_perubahan', 'jumlah_sesudah', 'aktivitas', 'created_at'
    ];
    
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = null;
    
    // Models lain yang dibutuhkan
    protected $produkModel;
    protected $kategoriModel;
    protected $db;
    
    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
        $this->produkModel = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
    }
    
    // ========== METHOD LOG STOK (Original) ==========
    
    /**
     * Get log stok with product and user info
     */
    public function getWithProduk($limit = 100)
    {
        return $this->select('log_stok.*, produk.nama_barang, produk.sku, users.username')
                    ->join('produk', 'produk.id = log_stok.id_produk', 'left')
                    ->join('users', 'users.user_id = log_stok.id_user', 'left')
                    ->orderBy('log_stok.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get log stok by product ID
     */
    public function getByProduk($id_produk, $limit = 100)
    {
        return $this->where('id_produk', $id_produk)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get log stok with filters and pagination
     */
    public function getFiltered($startDate, $endDate, $produkId = null, $tipe = null, $perPage = 50)
    {
        $builder = $this->select('log_stok.*, produk.nama_barang, produk.sku, users.username')
            ->join('produk', 'produk.id = log_stok.id_produk')
            ->join('users', 'users.user_id = log_stok.id_user', 'left')
            ->where('log_stok.created_at >=', $startDate . ' 00:00:00')
            ->where('log_stok.created_at <=', $endDate . ' 23:59:59');

        if ($produkId) {
            $builder->where('log_stok.id_produk', $produkId);
        }

        if ($tipe) {
            $builder->where('log_stok.tipe_ref', $tipe);
        }

        return $builder->orderBy('log_stok.created_at', 'DESC')->paginate($perPage);
    }
    
    /**
     * Get total mutasi stok (masuk/keluar) for a period
     */
    public function getMutasiStok($startDate, $endDate)
    {
        $totalMasuk = $this->where('tipe_ref', 'pembelian')
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->selectSum('jumlah_perubahan')
            ->first()['jumlah_perubahan'] ?? 0;

        $totalKeluar = $this->where('tipe_ref', 'penjualan')
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->selectSum('jumlah_perubahan')
            ->first()['jumlah_perubahan'] ?? 0;
        
        return [
            'masuk' => abs($totalMasuk),
            'keluar' => abs($totalKeluar)
        ];
    }
    
    // ========== METHOD MANAJEMEN STOK (Tambahan) ==========
    
    /**
     * Get all products with filters and pagination
     */
    public function getStokList($keyword = null, $kategoriId = null, $statusStok = null, $perPage = 10)
    {
        $builder = $this->produkModel
            ->select('produk.*, kategori.nama as nama_kategori')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left');

        if ($keyword) {
            $builder->groupStart()
                ->like('produk.nama_barang', $keyword)
                ->orLike('produk.sku', $keyword)
                ->groupEnd();
        }

        if ($kategoriId) {
            $builder->where('produk.id_kategori', $kategoriId);
        }

        if ($statusStok == 'menipis') {
            $builder->where('produk.stok <=', 'produk.min_stok', false);
        } elseif ($statusStok == 'habis') {
            $builder->where('produk.stok', 0);
        } elseif ($statusStok == 'aman') {
            $builder->where('produk.stok >', 'produk.min_stok', false);
        }

        return $builder->orderBy('produk.id', 'DESC')->paginate($perPage);
    }
    
    /**
     * Get pager for produk model
     */
    public function getPager()
    {
        return $this->produkModel->pager;
    }
    
    /**
     * Get stok statistics
     */
    public function getStokStatistics()
    {
        $totalProduk = $this->produkModel->countAll();
        $stokMenipis = $this->produkModel->where('stok <=', 'min_stok', false)->countAllResults();
        $stokHabis = $this->produkModel->where('stok', 0)->countAllResults();
        
        $totalNilai = $this->db->table('produk')
            ->select('SUM(stok * harga_beli) as total')
            ->get()
            ->getRow();
        $totalNilaiStok = $totalNilai->total ?? 0;
        
        return [
            'total_produk' => $totalProduk,
            'stok_menipis' => $stokMenipis,
            'stok_habis' => $stokHabis,
            'total_nilai_stok' => $totalNilaiStok
        ];
    }
    
    /**
     * Get product detail with stock history
     */
    public function getProductDetail($id)
    {
        $produk = $this->db->table('produk')
            ->select('produk.*, kategori.nama as nama_kategori, supplier.nama as nama_supplier')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->join('supplier', 'supplier.id = produk.id_supplier', 'left')
            ->where('produk.id', $id)
            ->get()
            ->getRowArray();
        
        if (!$produk) {
            return null;
        }
        
        $logStok = $this->db->table('log_stok')
            ->select('log_stok.*, users.username')
            ->join('users', 'users.user_id = log_stok.id_user', 'left')
            ->where('id_produk', $id)
            ->orderBy('created_at', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();
        
        return [
            'produk' => $produk,
            'log_stok' => $logStok
        ];
    }
    
    /**
     * Get product for opname
     */
    public function getProductForOpname($id)
    {
        return $this->produkModel->find($id);
    }
    
    /**
     * Update stock opname
     */
    public function updateOpname($id, $stokFisik, $keterangan, $userId)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return ['success' => false, 'error' => 'Produk tidak ditemukan'];
        }
        
        $stok_sebelum = $produk['stok'];
        $perubahan = $stokFisik - $stok_sebelum;
        
        if ($perubahan == 0) {
            return ['success' => true, 'message' => 'Stok sudah sesuai, tidak ada perubahan', 'perubahan' => 0];
        }
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            $this->produkModel->update($id, ['stok' => $stokFisik]);
            
            $this->insert([
                'id_produk' => $id,
                'id_user' => $userId,
                'tipe_ref' => 'penyesuaian',
                'id_ref' => null,
                'jumlah_sebelum' => $stok_sebelum,
                'jumlah_perubahan' => $perubahan,
                'jumlah_sesudah' => $stokFisik,
                'aktivitas' => 'Stok opname - ' . $keterangan,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            $db->transComplete();
            
            $message = $perubahan > 0
                ? "Stok bertambah " . abs($perubahan) . " unit"
                : "Stok berkurang " . abs($perubahan) . " unit";
            
            return ['success' => true, 'message' => $message, 'perubahan' => $perubahan];
            
        } catch (\Exception $e) {
            $db->transRollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Get all categories for filter
     */
    public function getKategoriList()
    {
        return $this->kategoriModel->findAll();
    }
    
    /**
     * Get all products for dropdown
     */
    public function getProductList()
    {
        return $this->produkModel->findAll();
    }
    
    /**
     * Get stock history with filters and pagination
     */
    public function getStockHistory($startDate, $endDate, $produkId = null, $tipe = null, $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        
        // Hitung total data
        $builderCount = $this->db->table('log_stok')
            ->select('COUNT(*) as total')
            ->join('produk', 'produk.id = log_stok.id_produk')
            ->join('users', 'users.user_id = log_stok.id_user', 'left')
            ->where('log_stok.created_at >=', $startDate . ' 00:00:00')
            ->where('log_stok.created_at <=', $endDate . ' 23:59:59');
        
        if ($produkId) {
            $builderCount->where('log_stok.id_produk', $produkId);
        }
        
        if ($tipe) {
            $builderCount->where('log_stok.tipe_ref', $tipe);
        }
        
        $totalData = $builderCount->get()->getRow()->total ?? 0;
        
        // Ambil data
        $builder = $this->db->table('log_stok')
            ->select('log_stok.*, produk.nama_barang, produk.sku, users.username')
            ->join('produk', 'produk.id = log_stok.id_produk')
            ->join('users', 'users.user_id = log_stok.id_user', 'left')
            ->where('log_stok.created_at >=', $startDate . ' 00:00:00')
            ->where('log_stok.created_at <=', $endDate . ' 23:59:59');
        
        if ($produkId) {
            $builder->where('log_stok.id_produk', $produkId);
        }
        
        if ($tipe) {
            $builder->where('log_stok.tipe_ref', $tipe);
        }
        
        $log = $builder->orderBy('log_stok.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();
        
        // Statistik
        $totalMasuk = $this->db->table('log_stok')
            ->selectSum('jumlah_perubahan')
            ->where('tipe_ref', 'pembelian')
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->get()
            ->getRowArray();

        $totalKeluar = $this->db->table('log_stok')
            ->selectSum('jumlah_perubahan')
            ->where('tipe_ref', 'penjualan')
            ->where('created_at >=', $startDate . ' 00:00:00')
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->get()
            ->getRowArray();
        
        return [
            'log' => $log,
            'total_data' => $totalData,
            'total_masuk' => abs($totalMasuk['jumlah_perubahan'] ?? 0),
            'total_keluar' => abs($totalKeluar['jumlah_perubahan'] ?? 0),
            'current_page' => $page,
            'per_page' => $perPage
        ];
    }
}