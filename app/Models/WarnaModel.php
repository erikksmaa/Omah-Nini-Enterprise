<?php

namespace App\Models;

use CodeIgniter\Model;

class WarnaModel extends Model
{
    protected $table = 'warna';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nama_warna',
        'created_at'
    ];

    // Timestamps hanya untuk created_at, tidak ada updated_at
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    // ========== VALIDATION RULES ==========
    protected $validationRules = [
        'nama_warna' => 'required|min_length[3]|max_length[50]|is_unique[warna.nama_warna,id,{id}]',
    ];

    protected $validationMessages = [
        'nama_warna' => [
            'required' => 'Nama warna wajib diisi.',
            'min_length' => 'Nama warna minimal 3 karakter.',
            'max_length' => 'Nama warna maksimal 50 karakter.',
            'is_unique' => 'Nama warna sudah terdaftar.'
        ],
    ];

    public function isUsed($id)
{
    $produkModel = new ProdukModel();
    return $produkModel->where('id_warna', $id)->countAllResults() > 0;
}

    public function getTotalWarna()
    {
        return $this->countAllResults();
    }

    /**
     * Get all warna with pagination
     */
    public function getAllPaginated($perPage = 10)
    {
        return $this->orderBy('nama_warna', 'ASC')->paginate($perPage);
    }

    /**
     * Get warna by ID
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * Get all warna for dropdown/select options
     */
    public function getOptions()
    {
        return $this->select('id, nama_warna')
            ->orderBy('nama_warna', 'ASC')
            ->findAll();
    }

    /**
     * Get dropdown array [id => nama_warna]
     */
    public function getDropdown()
    {
        $result = $this->orderBy('nama_warna', 'ASC')->findAll();
        $dropdown = [];
        foreach ($result as $row) {
            $dropdown[$row['id']] = $row['nama_warna'];
        }
        return $dropdown;
    }

    /**
     * Check if warna has related products
     */
    public function hasRelatedProducts($id)
    {
        $produkModel = new ProdukModel();
        return $produkModel->where('id_warna', $id)->countAllResults() > 0;
    }

    /**
     * Get count of products using this warna
     */
    public function getProductCount($id)
    {
        $produkModel = new ProdukModel();
        return $produkModel->where('id_warna', $id)->countAllResults();
    }

    /**
     * Search warna by name
     */
    public function search($keyword, $limit = 10)
    {
        return $this->like('nama_warna', $keyword)
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get warna with product count (for dashboard/stats)
     */
    public function getWithProductCount()
    {
        return $this->select('warna.*, COUNT(produk.id) as total_produk')
            ->join('produk', 'produk.id_warna = warna.id', 'left')
            ->groupBy('warna.id')
            ->orderBy('warna.nama_warna', 'ASC')
            ->findAll();
    }

    /**
     * Get most used colors (top 5)
     */
    public function getMostUsedColors($limit = 5)
    {
        return $this->select('warna.nama_warna, COUNT(produk.id) as total_produk')
            ->join('produk', 'produk.id_warna = warna.id', 'inner')
            ->groupBy('warna.id')
            ->orderBy('total_produk', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get warna by list of IDs
     */
    public function getByIds($ids)
    {
        if (empty($ids)) {
            return [];
        }
        return $this->whereIn('id', $ids)->orderBy('nama_warna', 'ASC')->findAll();
    }

    /**
     * Get total count of warna
     */
    public function getTotalCount()
    {
        return $this->countAllResults();
    }

    /**
     * Insert multiple warna at once (for initial seeding)
     */
    public function insertBatchWarna($data)
    {
        if (empty($data)) {
            return false;
        }

        $insertData = [];
        $now = date('Y-m-d H:i:s');

        foreach ($data as $item) {
            $insertData[] = [
                'nama_warna' => $item['nama_warna'],
                'created_at' => $now
            ];
        }

        return $this->db->table($this->table)->insertBatch($insertData);
    }

}