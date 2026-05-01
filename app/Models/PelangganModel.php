<?php

namespace App\Models;

use CodeIgniter\Model;

class PelangganModel extends Model
{
    protected $table            = 'pelanggan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama',
        'alamat',
        'no_telp',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // ========== VALIDATION RULES ==========
    protected $validationRules = [
        'nama'    => 'required|min_length[3]|max_length[100]',
        'alamat'  => 'permit_empty|max_length[500]',
        'no_telp' => 'permit_empty|min_length[8]|max_length[20]'
    ];

    protected $validationMessages = [
        'nama' => [
            'required'   => 'Nama pelanggan wajib diisi.',
            'min_length' => 'Nama pelanggan minimal 3 karakter.',
            'max_length' => 'Nama pelanggan maksimal 100 karakter.'
        ],
        'no_telp' => [
            'min_length' => 'Nomor telepon minimal 8 digit.',
            'max_length' => 'Nomor telepon maksimal 20 digit.'
        ]
    ];

    // ========== CUSTOM METHODS ==========

    /**
     * Get all pelanggan with pagination
     */
    public function getAllPaginated($perPage = 10)
    {
        return $this->orderBy('id', 'DESC')->paginate($perPage);
    }

    /**
     * Get pelanggan by ID
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * Get all pelanggan for dropdown/select options
     */
    public function getOptions()
    {
        return $this->select('id, nama, no_telp')
                    ->orderBy('nama', 'ASC')
                    ->findAll();
    }

    /**
     * Get dropdown array [id => nama]
     */
    public function getDropdown()
    {
        $result = $this->orderBy('nama', 'ASC')->findAll();
        $dropdown = [];
        foreach ($result as $row) {
            $dropdown[$row['id']] = $row['nama'];
        }
        return $dropdown;
    }

    /**
     * Search pelanggan by nama or no_telp
     */
    public function search($keyword, $limit = 10)
    {
        return $this->groupStart()
                    ->like('nama', $keyword)
                    ->orLike('no_telp', $keyword)
                    ->groupEnd()
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get total count of pelanggan
     */
    public function getTotalCount()
    {
        return $this->countAllResults();
    }

    /**
     * Get pelanggan with transaction count
     */
    public function getWithTransactionCount()
    {
        return $this->select('pelanggan.*, COUNT(transaksi.id) as total_transaksi')
                    ->join('transaksi', 'transaksi.id_pelanggan = pelanggan.id', 'left')
                    ->groupBy('pelanggan.id')
                    ->orderBy('pelanggan.nama', 'ASC')
                    ->findAll();
    }

    /**
     * Get top pelanggan by transaction count
     */
    public function getTopPelanggan($limit = 10)
    {
        return $this->select('pelanggan.id, pelanggan.nama, pelanggan.no_telp, COUNT(transaksi.id) as total_transaksi')
                    ->join('transaksi', 'transaksi.id_pelanggan = pelanggan.id', 'inner')
                    ->groupBy('pelanggan.id')
                    ->orderBy('total_transaksi', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}