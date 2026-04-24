<?php
namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['nama', 'deskripsi'];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // ========== VALIDATION RULES ==========
    protected $validationRules = [
        'nama' => 'required|min_length[3]|max_length[100]|is_unique[kategori.nama]',
        'deskripsi' => 'permit_empty|max_length[500]'
    ];

    protected $validationMessages = [
        'nama' => [
            'required' => 'Nama kategori wajib diisi.',
            'min_length' => 'Nama kategori minimal 3 karakter.',
            'max_length' => 'Nama kategori maksimal 100 karakter.',
            'is_unique' => 'Nama kategori sudah ada.'
        ],
        'deskripsi' => [
            'max_length' => 'Deskripsi maksimal 500 karakter.'
        ]
    ];

    // ========== CUSTOM METHODS ==========

    /**
     * Get all kategori with pagination
     */
    public function getAllPaginated($perPage = 10)
    {
        return $this->orderBy('id', 'DESC')->paginate($perPage);
    }

    /**
     * Get kategori by ID
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * Check if kategori has related products
     */
    public function hasRelatedProducts($id)
    {
        $produkModel = new ProdukModel();
        return $produkModel->where('id_kategori', $id)->countAllResults() > 0;
    }

    /**
     * Get all kategori for dropdown/select options
     */
    public function getOptions()
    {
        return $this->select('id, nama')->orderBy('nama', 'ASC')->findAll();
    }

    /**
     * Get count of products in kategori
     */
    public function getProductCount($id)
    {
        $produkModel = new ProdukModel();
        return $produkModel->where('id_kategori', $id)->countAllResults();
    }
}