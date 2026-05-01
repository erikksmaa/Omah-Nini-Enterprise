<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'sku',
        'id_supplier',
        'id_motif',
        'id_warna',
        'stok',
        'min_stok',
        'keterangan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // ========== VALIDATION RULES ==========
    protected $validationRules = [
        'sku' => 'required',  // Hanya required, tanpa is_unique
        'id_supplier' => 'required|numeric|is_not_unique[supplier.id]',
        'id_motif' => 'required|numeric|is_not_unique[motif.id]',
        'id_warna' => 'required|numeric|is_not_unique[warna.id]',
        'stok' => 'required|numeric|greater_than_equal_to[0]',
        'min_stok' => 'permit_empty|numeric|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'sku' => [
            'required' => 'SKU wajib diisi.',
            'is_unique' => 'SKU sudah terdaftar.'
        ],
        'id_supplier' => [
            'required' => 'Supplier wajib dipilih.'
        ],
        'id_motif' => [
            'required' => 'Motif wajib dipilih.'
        ],
        'id_warna' => [
            'required' => 'Warna wajib dipilih.'
        ],
        'stok' => [
            'required' => 'Stok wajib diisi.',
            'greater_than_equal_to' => 'Stok tidak boleh negatif.'
        ]
    ];

    // ========== CUSTOM METHODS ==========

    /**
     * Get full product data with relations (supplier, motif, warna)
     */
    public function getFullData($id = null)
    {
        $builder = $this->select('produk.*, supplier.nama as nama_supplier, motif.nama_motif, warna.nama_warna')
            ->join('supplier', 'supplier.id = produk.id_supplier')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna');

        if ($id) {
            return $builder->where('produk.id', $id)->first();
        }

        return $builder->orderBy('produk.id', 'DESC')->findAll();
    }

    /**
     * Get all produk with relations (with pagination)
     */
    public function getAllWithRelations($perPage = 10)
    {
        return $this->select('produk.*, supplier.nama as nama_supplier, motif.nama_motif, warna.nama_warna')
            ->join('supplier', 'supplier.id = produk.id_supplier')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->orderBy('produk.id', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Get produk by ID with relations
     */
    public function getByIdWithRelations($id)
    {
        return $this->select('produk.*, supplier.nama as nama_supplier, supplier.id as supplier_id, motif.nama_motif, motif.id as motif_id, warna.nama_warna, warna.id as warna_id')
            ->join('supplier', 'supplier.id = produk.id_supplier')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->find($id);
    }

    /**
     * Get produk for dropdown (for form pembelian/penjualan)
     */
    public function getOptions()
    {
        return $this->select('produk.id, motif.nama_motif, warna.nama_warna, produk.stok')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->orderBy('motif.nama_motif', 'ASC')
            ->findAll();
    }

    /**
     * Get available products (stok > 0) for POS
     */
    public function getAvailableProducts()
    {
        return $this->select('produk.id, produk.sku, motif.nama_motif, warna.nama_warna, produk.stok')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->where('produk.stok >', 0)
            ->orderBy('motif.nama_motif', 'ASC')
            ->findAll();
    }

    /**
     * Search produk by keyword (motif name, warna name, or sku)
     */
    public function search($keyword, $limit = 10)
    {
        return $this->select('produk.id, produk.sku, motif.nama_motif, warna.nama_warna, produk.stok')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->groupStart()
            ->like('produk.sku', $keyword)
            ->orLike('motif.nama_motif', $keyword)
            ->orLike('warna.nama_warna', $keyword)
            ->groupEnd()
            ->where('produk.stok >', 0)
            ->limit($limit)
            ->findAll();
    }

    /**
     * Update stok produk with validation (tidak bisa minus)
     */
    public function updateStock($id, $jumlah, $tipe = 'tambah')
    {
        $produk = $this->find($id);
        if (!$produk) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan'];
        }

        $stok_sekarang = $produk['stok'];

        if ($tipe == 'tambah') {
            $stok_baru = $stok_sekarang + $jumlah;
        } else {
            if ($stok_sekarang < $jumlah) {
                return ['success' => false, 'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $stok_sekarang];
            }
            $stok_baru = $stok_sekarang - $jumlah;
        }

        $result = $this->update($id, ['stok' => $stok_baru]);

        if ($result) {
            return ['success' => true, 'stok_baru' => $stok_baru];
        }

        return ['success' => false, 'message' => 'Gagal update stok'];
    }

    /**
     * Get produk with low stock
     */
    public function getLowStockProducts($limit = null)
    {
        $builder = $this->select('produk.*, supplier.nama as nama_supplier, motif.nama_motif, warna.nama_warna')
            ->join('supplier', 'supplier.id = produk.id_supplier')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->where('produk.stok <= produk.min_stok')
            ->where('produk.stok >', 0)
            ->orderBy('produk.stok', 'ASC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get out of stock products (stok = 0)
     */
    public function getOutOfStockProducts()
    {
        return $this->select('produk.*, supplier.nama as nama_supplier, motif.nama_motif, warna.nama_warna')
            ->join('supplier', 'supplier.id = produk.id_supplier')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->where('produk.stok', 0)
            ->orderBy('motif.nama_motif', 'ASC')
            ->findAll();
    }

    /**
     * Get produk with highest stock
     */
    public function getHighestStockProducts($limit = 10)
    {
        return $this->select('produk.*, supplier.nama as nama_supplier, motif.nama_motif, warna.nama_warna')
            ->join('supplier', 'supplier.id = produk.id_supplier')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->orderBy('produk.stok', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get total stock quantity
     */
    public function getTotalStockQuantity()
    {
        $result = $this->select('SUM(stok) as total')->first();
        return $result['total'] ?? 0;
    }

    /**
     * Check if SKU exists (for validation)
     */
    public function isSkuExists($sku, $excludeId = null)
    {
        $query = $this->where('sku', $sku);
        if ($excludeId) {
            $query->where('id !=', $excludeId);
        }
        return $query->countAllResults() > 0;
    }

    /**
     * Generate SKU otomatis
     */
    public function generateSku($id_supplier, $id_motif, $id_warna)
    {
        $supplierModel = new SupplierModel();
        $motifModel = new MotifModel();
        $warnaModel = new WarnaModel();

        $supplier = $supplierModel->find($id_supplier);
        $motif = $motifModel->find($id_motif);
        $warna = $warnaModel->find($id_warna);

        $supplierCode = $this->getSupplierCode($supplier['nama']);
        $motifCode = $this->generateCode($motif['nama_motif'], 3);
        $warnaCode = $this->generateCode($warna['nama_warna'], 3);

        $like = $supplierCode . '-' . $motifCode . '-' . $warnaCode . '-%';
        $last = $this->like('sku', $like, 'after')->orderBy('id', 'DESC')->first();

        if ($last) {
            $lastSeq = explode('-', $last['sku']);
            $seq = intval(end($lastSeq)) + 1;
        } else {
            $seq = 1;
        }

        $sequence = str_pad($seq, 3, '0', STR_PAD_LEFT);

        return $supplierCode . '-' . $motifCode . '-' . $warnaCode . '-' . $sequence;
    }

    private function getSupplierCode($nama, $id_supplier = null)
    {
        $codes = [
            'Bang Jack\'s / Aulia' => 'BJK',
            'Maida Exclusive' => 'MDX',
            'Maida Katun Super' => 'MDK',
            'AL FATI' => 'AFT'
        ];

        // Jika sudah di predefined, pakai kode tersebut
        if (isset($codes[$nama])) {
            return $codes[$nama];
        }

        // Generate dari nama (3 huruf pertama)
        $code = substr(preg_replace('/[^A-Z]/', '', strtoupper($nama)), 0, 3);

        // Cek apakah kode sudah digunakan oleh supplier lain
        $supplierModel = new SupplierModel();
        $existing = $supplierModel->select('id, nama')
            ->like('nama', $code)
            ->first();

        if ($existing && $existing['nama'] != $nama) {
            // Jika tabrakan, tambahkan angka
            $code = $code . rand(1, 9);
        }

        return $code;
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