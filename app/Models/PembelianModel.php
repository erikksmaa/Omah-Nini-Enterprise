<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\ProdukModel;

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

    public function getCountPembelianHariIni()
    {
        return $this->where('DATE(tanggal_pembelian)', date('Y-m-d'))->countAllResults();
    }

    public function getCountPembelianBulanIni()
    {
        return $this->where('MONTH(tanggal_pembelian)', date('m'))
            ->where('YEAR(tanggal_pembelian)', date('Y'))
            ->countAllResults();
    }

    public function getRecentPurchases($limit = 10)
    {
        return $this->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->orderBy('pembelian.id', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getTotalPembelian()
    {
        return $this->countAllResults();
    }

    public function getAllWithSupplier($perPage = 10)
    {
        return $this->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->orderBy('pembelian.id', 'DESC')
            ->paginate($perPage);
    }

    public function getByIdWithSupplier($id)
    {
        return $this->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->find($id);
    }

    public function getDetail($id)
    {
        return $this->select('pembelian.*, supplier.nama as supplier_nama')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->where('pembelian.id', $id)
            ->first();
    }

    // ========== INVOICE GENERATOR (LOCK-SAFE) ==========

    private function _generateNoInvoice()
    {
        $db = \Config\Database::connect();
        $todayPrefix = 'PO-' . date('ymd') . '-';

        $builder = $db->table('pembelian');
        $lastRecord = $builder
            ->select('no_invoice')
            ->like('no_invoice', $todayPrefix, 'after')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();

        if ($lastRecord && isset($lastRecord->no_invoice)) {
            $lastNumber = (int) substr($lastRecord->no_invoice, -4);
            $newNumber  = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $todayPrefix . $newNumber;
    }

    // ========== SIMPAN PEMBELIAN DENGAN DETAIL ==========

    public function savePembelian($data, $items, $userId)
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $db->transStart();

        try {
            // 1. Generate invoice dengan lock (dalam transaksi)
            $invoice = $this->_generateNoInvoice();
            $data['no_invoice'] = $invoice;

            // 2. Insert header
            $db->table('pembelian')->insert($data);
            $pembelianId = $db->insertID();

            // 3. Insert detail, update stok, log
            $produkModel = new ProdukModel();
            foreach ($items as $item) {
                // Validasi jumlah
                if (empty($item['id_produk']) || $item['jumlah'] <= 0) {
                    throw new \Exception("Jumlah produk harus lebih dari 0.");
                }

                // Ambil data produk lengkap (supplier, motif, warna)
                $produk = $produkModel->getFullData($item['id_produk']);
                if (!$produk) {
                    throw new \Exception("Produk dengan ID {$item['id_produk']} tidak ditemukan.");
                }

                // Format baru: "merek - motif - warna"
                $namaProduk = $produk['nama_supplier'] . ' - ' . $produk['nama_motif'] . ' ' . $produk['nama_warna'];

                // Insert detail
                $db->table('detail_pembelian')->insert([
                    'id_pembelian' => $pembelianId,
                    'id_produk'    => $item['id_produk'],
                    'nama_produk'  => $namaProduk,
                    'jumlah'       => $item['jumlah']
                ]);

                // Update stok
                $stokLama = $produk['stok'];
                $stokBaru = $stokLama + $item['jumlah'];
                $db->table('produk')->where('id', $item['id_produk'])->update(['stok' => $stokBaru]);

                // Log stok
                $db->table('log_stok')->insert([
                    'id_produk'        => $item['id_produk'],
                    'id_user'          => $userId,
                    'tipe_ref'         => 'pembelian',
                    'id_ref'           => $pembelianId,
                    'jumlah_sebelum'   => $stokLama,
                    'jumlah_perubahan' => $item['jumlah'],
                    'jumlah_sesudah'   => $stokBaru,
                    'created_at'       => $now
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal.');
            }

            return ['success' => true, 'id' => $pembelianId, 'no_invoice' => $invoice];
        } catch (\Exception $e) {
            $db->transRollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}