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
        'nama_barang',
        'id_kategori',
        'id_supplier',
        'harga_beli',
        'harga_jual',
        'stok',
        'min_stok',
        'keterangan'
    ];

    // Matikan timestamps jika kolom created_at/updated_at tidak ada atau bermasalah
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // ========== CUSTOM METHODS ==========

    /**
     * Get all produk with kategori and supplier info (with pagination)
     */
    public function getAllWithRelations($perPage = 10)
    {
        return $this->select('produk.*, kategori.nama as nama_kategori, supplier.nama as nama_supplier')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->join('supplier', 'supplier.id = produk.id_supplier', 'left')
            ->orderBy('produk.id', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Get produk by ID with relations
     */
    public function getByIdWithRelations($id)
    {
        return $this->select('produk.*, kategori.nama as nama_kategori, supplier.nama as nama_supplier')
            ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
            ->join('supplier', 'supplier.id = produk.id_supplier', 'left')
            ->find($id);
    }

    /**
     * Get all produk for dropdown/select options (only id and name)
     */
    public function getOptions()
    {
        return $this->select('id, nama_barang, sku, harga_jual, stok')
            ->orderBy('nama_barang', 'ASC')
            ->findAll();
    }

    /**
     * Get produk with stok > 0 (for POS)
     */
    public function getAvailableProducts()
    {
        return $this->where('stok >', 0)->orderBy('nama_barang', 'ASC')->findAll();
    }

    /**
     * Search produk by keyword (name or sku)
     */
    public function search($keyword, $limit = 10)
    {
        return $this->groupStart()
            ->like('nama_barang', $keyword)
            ->orLike('sku', $keyword)
            ->groupEnd()
            ->where('stok >', 0)
            ->limit($limit)
            ->findAll();
    }

    /**
     * Update stok produk
     */
    public function updateStock($id, $newStock)
    {
        return $this->update($id, ['stok' => $newStock]);
    }

    /**
     * Get produk with low stock (stok <= min_stok)
     */
    public function getLowStockProducts($limit = null)
    {
        $query = $this->where('stok <=', 'min_stok', false)->orderBy('stok', 'ASC');
        if ($limit) {
            $query->limit($limit);
        }
        return $query->findAll();
    }

    /**
     * Get produk with highest stock
     */
    public function getHighestStockProducts($limit = 10)
    {
        return $this->orderBy('stok', 'DESC')->limit($limit)->findAll();
    }

    /**
     * Get total stock value (stok * harga_beli)
     */
    public function getTotalStockValue()
    {
        $result = $this->db->table($this->table)
            ->select('SUM(stok * harga_beli) as total')
            ->get()
            ->getRow();
        return $result->total ?? 0;
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
}