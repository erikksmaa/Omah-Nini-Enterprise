<?php

namespace App\Models;

use CodeIgniter\Model;

class MotifModel extends Model
{
    protected $table = 'motif';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_supplier',
        'nama_motif',
        'keterangan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // ========== VALIDATION RULES ==========
    protected $validationRules = [
        'id_supplier' => 'required|numeric|is_not_unique[supplier.id]',
        'nama_motif' => 'required|min_length[3]|max_length[100]'
    ];

    protected $validationMessages = [
        'id_supplier' => [
            'required' => 'Supplier wajib dipilih.',
            'numeric' => 'Supplier tidak valid.'
        ],
        'nama_motif' => [
            'required' => 'Nama motif wajib diisi.',
            'min_length' => 'Nama motif minimal 3 karakter.',
            'max_length' => 'Nama motif maksimal 100 karakter.'
        ]
    ];

    public function getTotalMotif()
    {
        return $this->countAllResults();
    }

    /**
     * Get all motif with supplier info and pagination
     */
    public function getAllWithSupplier($perPage = 10)
    {
        return $this->select('motif.*, supplier.nama as nama_supplier')
            ->join('supplier', 'supplier.id = motif.id_supplier')
            ->orderBy('supplier.nama', 'ASC')
            ->orderBy('motif.nama_motif', 'ASC')
            ->paginate($perPage);
    }

    /**
     * Get motif by ID with supplier info
     */
    public function getByIdWithSupplier($id)
    {
        return $this->select('motif.*, supplier.nama as nama_supplier')
            ->join('supplier', 'supplier.id = motif.id_supplier')
            ->find($id);
    }

    /**
     * Get motif by supplier ID
     */
    public function getBySupplier($id_supplier)
    {
        return $this->where('id_supplier', $id_supplier)
            ->orderBy('nama_motif', 'ASC')
            ->findAll();
    }

    /**
     * Get motif by supplier ID for dropdown
     */
    public function getOptionsBySupplier($id_supplier)
    {
        return $this->select('id, nama_motif')
            ->where('id_supplier', $id_supplier)
            ->orderBy('nama_motif', 'ASC')
            ->findAll();
    }

    /**
     * Get all motif for dropdown/select options (with supplier name)
     */
    public function getOptions()
    {
        return $this->select('motif.id, motif.nama_motif, supplier.nama as nama_supplier')
            ->join('supplier', 'supplier.id = motif.id_supplier')
            ->orderBy('supplier.nama', 'ASC')
            ->orderBy('motif.nama_motif', 'ASC')
            ->findAll();
    }

    /**
     * Get dropdown array group by supplier
     * Returns: [supplier_id => [motif_id => motif_name]]
     */
    public function getDropdownGroupBySupplier()
    {
        $result = $this->select('motif.id, motif.nama_motif, motif.id_supplier, supplier.nama as nama_supplier')
            ->join('supplier', 'supplier.id = motif.id_supplier')
            ->orderBy('supplier.nama', 'ASC')
            ->orderBy('motif.nama_motif', 'ASC')
            ->findAll();

        $dropdown = [];
        foreach ($result as $row) {
            $supplierId = $row['id_supplier'];
            if (!isset($dropdown[$supplierId])) {
                $dropdown[$supplierId] = [
                    'nama_supplier' => $row['nama_supplier'],
                    'motifs' => []
                ];
            }
            $dropdown[$supplierId]['motifs'][$row['id']] = $row['nama_motif'];
        }
        return $dropdown;
    }

    /**
     * Check if motif has related products
     */
    public function hasRelatedProducts($id)
    {
        $produkModel = new ProdukModel();
        return $produkModel->where('id_motif', $id)->countAllResults() > 0;
    }

    /**
     * Get count of products using this motif
     */
    public function getProductCount($id)
    {
        $produkModel = new ProdukModel();
        return $produkModel->where('id_motif', $id)->countAllResults();
    }

    /**
     * Search motif by name or supplier name
     */
    public function search($keyword, $limit = 10)
    {
        return $this->select('motif.*, supplier.nama as nama_supplier')
            ->join('supplier', 'supplier.id = motif.id_supplier')
            ->groupStart()
            ->like('motif.nama_motif', $keyword)
            ->orLike('supplier.nama', $keyword)
            ->groupEnd()
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get motif with product count (for dashboard/stats)
     */
    public function getWithProductCount()
    {
        return $this->select('motif.*, supplier.nama as nama_supplier, COUNT(produk.id) as total_produk')
            ->join('supplier', 'supplier.id = motif.id_supplier')
            ->join('produk', 'produk.id_motif = motif.id', 'left')
            ->groupBy('motif.id')
            ->orderBy('supplier.nama', 'ASC')
            ->orderBy('motif.nama_motif', 'ASC')
            ->findAll();
    }

    /**
     * Get motif with total stock value
     */
    public function getWithStockValue()
    {
        return $this->select('motif.*, supplier.nama as nama_supplier, SUM(produk.stok * produk.harga_jual) as total_nilai_stok')
            ->join('supplier', 'supplier.id = motif.id_supplier')
            ->join('produk', 'produk.id_motif = motif.id', 'left')
            ->where('produk.stok >', 0)
            ->groupBy('motif.id')
            ->orderBy('total_nilai_stok', 'DESC')
            ->findAll();
    }

    /**
     * Get motif by list of IDs
     */
    public function getByIds($ids)
    {
        if (empty($ids)) {
            return [];
        }
        return $this->select('motif.*, supplier.nama as nama_supplier')
            ->join('supplier', 'supplier.id = motif.id_supplier')
            ->whereIn('motif.id', $ids)
            ->orderBy('motif.nama_motif', 'ASC')
            ->findAll();
    }

    /**
     * Get total count of motif
     */
    public function getTotalCount()
    {
        return $this->countAllResults();
    }

    /**
     * Get count of motif by supplier
     */
    public function getCountBySupplier($id_supplier)
    {
        return $this->where('id_supplier', $id_supplier)->countAllResults();
    }

    /**
     * Get top motifs by sales (best selling)
     */
    public function getTopMotifsBySales($limit = 10, $startDate = null, $endDate = null)
    {
        $builder = $this->select('motif.id, motif.nama_motif, supplier.nama as nama_supplier, SUM(detail_transaksi.jumlah) as total_terjual')
            ->join('supplier', 'supplier.id = motif.id_supplier')
            ->join('produk', 'produk.id_motif = motif.id')
            ->join('detail_transaksi', 'detail_transaksi.id_produk = produk.id')
            ->join('transaksi', 'transaksi.id = detail_transaksi.id_transaksi')
            ->where('transaksi.status', 'selesai')
            ->groupBy('motif.id')
            ->orderBy('total_terjual', 'DESC')
            ->limit($limit);

        if ($startDate && $endDate) {
            $builder->where('transaksi.tanggal_transaksi >=', $startDate)
                ->where('transaksi.tanggal_transaksi <=', $endDate);
        }

        return $builder->findAll();
    }

    /**
     * Insert multiple motif at once (for initial seeding)
     */
    public function insertBatchMotif($data)
    {
        if (empty($data)) {
            return false;
        }

        $insertData = [];
        $now = date('Y-m-d H:i:s');

        foreach ($data as $item) {
            $insertData[] = [
                'id_supplier' => $item['id_supplier'],
                'nama_motif' => $item['nama_motif'],
                'keterangan' => $item['keterangan'] ?? null,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        return $this->db->table($this->table)->insertBatch($insertData);
    }

    /**
     * Get motif with available colors (warna) for POS display
     */
    public function getMotifWithWarna($id_motif)
    {
        $motif = $this->getByIdWithSupplier($id_motif);
        if (!$motif) {
            return null;
        }

        $produkModel = new ProdukModel();
        $warnaModel = new WarnaModel();

        $produkList = $produkModel->select('produk.id, produk.stok, produk.harga_jual, warna.id as warna_id, warna.nama_warna, warna.kode_hex')
            ->join('warna', 'warna.id = produk.id_warna')
            ->where('produk.id_motif', $id_motif)
            ->where('produk.stok >', 0)
            ->orderBy('warna.nama_warna', 'ASC')
            ->findAll();

        $motif['warna_list'] = $produkList;
        return $motif;
    }

    /**
     * Get all motif with their available colors (for POS catalog)
     */
    public function getAllMotifWithWarna()
    {
        $motifs = $this->getAllWithSupplier();
        $warnaModel = new WarnaModel();
        $produkModel = new ProdukModel();

        foreach ($motifs as &$motif) {
            $produkList = $produkModel->select('produk.id, produk.stok, produk.harga_jual, warna.id as warna_id, warna.nama_warna, warna.kode_hex')
                ->join('warna', 'warna.id = produk.id_warna')
                ->where('produk.id_motif', $motif['id'])
                ->where('produk.stok >', 0)
                ->orderBy('warna.nama_warna', 'ASC')
                ->findAll();

            $motif['warna_list'] = $produkList;
            $motif['total_variant'] = count($produkList);
        }

        return $motifs;
    }
}