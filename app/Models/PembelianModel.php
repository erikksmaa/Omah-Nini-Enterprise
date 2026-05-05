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
        'no_invoice',
        'id_supplier',
        'id_user',
        'tanggal_pembelian',
        'catatan',
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    protected $validationRules = [
        'no_invoice' => 'required|is_unique[pembelian.no_invoice]',
        'id_supplier' => 'required|is_not_unique[supplier.id]',
        'id_user' => 'required|is_not_unique[users.user_id]',
    ];


    // ========== QUERY DASHBOARD ==========

    /**
     * Get count of purchases today
     */
    public function getCountPembelianHariIni()
    {
        return $this->where('DATE(tanggal_pembelian)', date('Y-m-d'))->countAllResults();
    }

    /**
     * Get count of purchases this month
     */
    public function getCountPembelianBulanIni()
    {
        return $this->where('MONTH(tanggal_pembelian)', date('m'))
            ->where('YEAR(tanggal_pembelian)', date('Y'))
            ->countAllResults();
    }

    /**
     * Get recent purchases
     */
    public function getRecentPurchases($limit = 10)
    {
        return $this->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->orderBy('pembelian.id', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get total count of purchases
     */
    public function getTotalPembelian()
    {
        return $this->countAllResults();
    }


    /**
     * Get all pembelian with supplier info, paginated
     */
    public function getAllWithSupplier($perPage = 10)
    {
        return $this->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->orderBy('pembelian.id', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Get single pembelian with supplier info
     */
    public function getByIdWithSupplier($id)
    {
        return $this->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->find($id);
    }

    /**
     * Get detail pembelian (header + supplier)
     */
    public function getDetail($id)
    {
        return $this->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->where('pembelian.id', $id)
            ->first();
    }

    /**
     * Generate nomor invoice: PO-YYMMDD-XXXX
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
     * Simpan pembelian beserta detail, update stok, catat log
     */
    public function savePembelian($data, $items, $userId)
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $db->transStart();

        try {
            // 1. Insert header pembelian
            $db->table('pembelian')->insert($data);
            $pembelianId = $db->insertID();

            // 2. Insert detail & update stok & log
            foreach ($items as $item) {
                // Insert detail
                $db->table('detail_pembelian')->insert([
                    'id_pembelian' => $pembelianId,
                    'id_produk' => $item['id_produk'],
                    'nama_produk' => $item['nama_produk'],
                    'jumlah' => $item['jumlah']
                ]);

                // Ambil stok sekarang
                $produk = $db->table('produk')->where('id', $item['id_produk'])->get()->getRowArray();
                $stokLama = $produk['stok'];
                $stokBaru = $stokLama + $item['jumlah'];

                // Update stok produk
                $db->table('produk')->where('id', $item['id_produk'])->update(['stok' => $stokBaru]);

                // Catat log stok
                $db->table('log_stok')->insert([
                    'id_produk' => $item['id_produk'],
                    'id_user' => $userId,
                    'tipe_ref' => 'pembelian',
                    'id_ref' => $pembelianId,
                    'jumlah_sebelum' => $stokLama,
                    'jumlah_perubahan' => $item['jumlah'],
                    'jumlah_sesudah' => $stokBaru,
                    'created_at' => $now
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal');
            }

            return ['success' => true, 'id' => $pembelianId];
        } catch (\Exception $e) {
            $db->transRollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get count pembelian this month
     */
   
}
