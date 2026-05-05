<?php
namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table = 'supplier';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['nama', 'kontak', 'email', 'alamat'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // ========== VALIDATION RULES ==========
    protected $validationRules = [
        'nama' => 'required|min_length[3]|max_length[100]',
        'kontak' => 'required|min_length[10]|max_length[15]',
        'email' => 'permit_empty|valid_email'
    ];

    protected $validationMessages = [
        'nama' => [
            'required' => 'Nama supplier wajib diisi.',
            'min_length' => 'Nama supplier minimal 3 karakter.',
            'max_length' => 'Nama supplier maksimal 100 karakter.'
        ],
        'kontak' => [
            'required' => 'Kontak wajib diisi.',
            'min_length' => 'Kontak minimal 10 digit.',
            'max_length' => 'Kontak maksimal 15 digit.'
        ],
        'email' => [
            'valid_email' => 'Format email tidak valid.'
        ]
    ];


    // ========== QUERY DASHBOARD ==========

    /**
     * Get total count of suppliers
     */
    public function getTotalSupplier()
    {
        return $this->countAllResults();
    }

    /**
     * Get all suppliers for dropdown
     */
    public function getOptions()
    {
        return $this->select('id, nama')->orderBy('nama', 'ASC')->findAll();
    }


    // ========== CUSTOM METHODS ==========

    /**
     * Get all supplier with pagination
     */
    public function getAllPaginated($perPage = 10)
    {
        return $this->orderBy('id', 'DESC')->paginate($perPage);
    }

    /**
     * Get supplier by ID
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * Get all supplier for dropdown/select options
     */

    /**
     * Check if supplier has related products
     */
    public function hasRelatedProducts($id)
    {
        $produkModel = new ProdukModel();
        return $produkModel->where('id_supplier', $id)->countAllResults() > 0;
    }

    /**
     * Get count of products from supplier
     */
    public function getProductCount($id)
    {
        $produkModel = new ProdukModel();
        return $produkModel->where('id_supplier', $id)->countAllResults();
    }

    /**
     * Search supplier by name or kontak
     */
    public function search($keyword, $limit = 10)
    {
        return $this->groupStart()
            ->like('nama', $keyword)
            ->orLike('kontak', $keyword)
            ->orLike('email', $keyword)
            ->groupEnd()
            ->limit($limit)
            ->findAll();
    }
}