<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Models\ProdukModel;

class TransaksiModel extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'no_invoice',
        'id_user',
        'id_pelanggan',
        'nama_pembeli',
        'tanggal_transaksi',
        'catatan',
        'created_at'
    ];

    // ========== QUERY DASHBOARD ==========

    /**
     * Get count of transactions today
     */
    public function getCountTransactionsToday()
    {
        return $this->where('DATE(tanggal_transaksi)', date('Y-m-d'))->countAllResults();
    }

    /**
     * Get count of transactions this month
     */
    public function getCountTransactionsThisMonth()
    {
        return $this->where('MONTH(tanggal_transaksi)', date('m'))
            ->where('YEAR(tanggal_transaksi)', date('Y'))
            ->countAllResults();
    }

    /**
     * Get weekly transactions count for chart
     */
    public function getWeeklyCount()
    {
        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $count = $this->where('DATE(tanggal_transaksi)', $date)->countAllResults();
            $result[] = [
                'date' => date('d/m', strtotime($date)),
                'total' => $count
            ];
        }
        return $result;
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactions($limit = 10)
    {
        return $this->select('transaksi.*, pelanggan.nama as nama_pelanggan')
            ->join('pelanggan', 'pelanggan.id = transaksi.id_pelanggan', 'left')
            ->orderBy('transaksi.id', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getCountTransactionsLastMonth()
    {
        $lastMonth = date('m', strtotime('-1 month'));
        $year = date('Y', strtotime('-1 month'));

        return $this->where('MONTH(tanggal_transaksi)', $lastMonth)
            ->where('YEAR(tanggal_transaksi)', $year)
            ->countAllResults();
    }

    // ========== INVOICE GENERATOR ==========
    // public function generateNoInvoice()
    // {
    //     $last = $this->orderBy('id', 'DESC')->first();
    //     if ($last && isset($last['no_invoice'])) {
    //         $lastNumber = (int) substr($last['no_invoice'], -4);
    //         $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    //     } else {
    //         $newNumber = '0001';
    //     }
    //     return 'INV-' . date('ymd') . '-' . $newNumber;
    // }

    private function _generateNoInvoice()
    {
        $db = \Config\Database::connect();
        $prefix = 'INV-' . date('ymd') . '-';
        $last = $db->table('transaksi')
            ->select('no_invoice')
            ->like('no_invoice', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
        if ($last && isset($last->no_invoice)) {
            $num = (int) substr($last->no_invoice, -4);
            $new = str_pad($num + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $new = '0001';
        }
        return $prefix . $new;
    }

    // ========== GET ALL WITH PAGINATION ==========
    public function getAllWithPelanggan($perPage = 10)
    {
        return $this->select('transaksi.*, pelanggan.nama as nama_pelanggan')
            ->join('pelanggan', 'pelanggan.id = transaksi.id_pelanggan', 'left')
            ->orderBy('transaksi.id', 'DESC')
            ->paginate($perPage);
    }

    // ========== GET SINGLE FOR STRUK / DETAIL ==========
    public function getDetail($id)
    {
        return $this->select('transaksi.*, pelanggan.nama as nama_pelanggan')
            ->join('pelanggan', 'pelanggan.id = transaksi.id_pelanggan', 'left')
            ->where('transaksi.id', $id)
            ->first();
    }

    // ========== SAVE TRANSAKSI + DETAIL + UPDATE STOK + LOG ==========
    public function saveTransaksi($headerData, $items, $userId)
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $db->transStart();
        try {
            $invoice = $this->_generateNoInvoice();
            $headerData['no_invoice'] = $invoice;
            $db->table('transaksi')->insert($headerData);
            $transaksiId = $db->insertID();

            $produkModel = new ProdukModel();
            foreach ($items as $item) {
                // validasi jumlah
                if ($item['jumlah'] <= 0) {
                    throw new \Exception("Jumlah harus lebih dari 0.");
                }

                $produk = $produkModel->getFullData($item['id_produk']);
                if (!$produk) {
                    throw new \Exception("Produk ID {$item['id_produk']} tidak ditemukan.");
                }

                // Format nama_produk baru
                $namaProduk = $produk['nama_supplier'] . ' - ' . $produk['nama_motif'] . ' ' . $produk['nama_warna'];

                // Insert detail dengan harga_satuan
                $db->table('detail_transaksi')->insert([
                    'id_transaksi' => $transaksiId,
                    'id_produk' => $item['id_produk'],
                    'nama_produk' => $namaProduk,
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'] ?? 0
                ]);

                // Kurangi stok
                $stokLama = $produk['stok'];
                if ($stokLama < $item['jumlah']) {
                    throw new \Exception("Stok tidak cukup untuk produk {$namaProduk}. Tersedia: {$stokLama}");
                }
                $stokBaru = $stokLama - $item['jumlah'];
                $db->table('produk')->where('id', $item['id_produk'])->update(['stok' => $stokBaru]);

                // Log stok
                $db->table('log_stok')->insert([
                    'id_produk' => $item['id_produk'],
                    'id_user' => $userId,
                    'tipe_ref' => 'penjualan',
                    'id_ref' => $transaksiId,
                    'jumlah_sebelum' => $stokLama,
                    'jumlah_perubahan' => $item['jumlah'],
                    'jumlah_sesudah' => $stokBaru,
                    'created_at' => $now
                ]);
            }

            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal.');
            }
            return ['success' => true, 'id' => $transaksiId, 'no_invoice' => $invoice];
        } catch (\Exception $e) {
            $db->transRollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get top selling products
     */
    public function getTopProducts($limit = 5)
    {
        return $this->db->table('detail_transaksi')
            ->select('motif.nama_motif, warna.nama_warna, SUM(detail_transaksi.jumlah) as total_terjual')
            ->join('produk', 'produk.id = detail_transaksi.id_produk')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->groupBy('produk.id')
            ->orderBy('total_terjual', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get recent activities
     */
    /**
     * Get recent activities with user info
     */
    public function getRecentActivities($limit = 10)
    {
        // Ambil 10 pembelian terbaru
        $pembelian = $this->db->table('pembelian')
            ->select("'pembelian' as tipe, pembelian.no_invoice as ref, pembelian.tanggal_pembelian as tanggal, CONCAT('Pembelian dari ', supplier.nama) as deskripsi, users.username as user, 'masuk' as icon")
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->join('users', 'users.user_id = pembelian.id_user')
            ->orderBy('pembelian.tanggal_pembelian', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        // Ambil 10 penjualan terbaru
        $penjualan = $this->db->table('transaksi')
            ->select("'penjualan' as tipe, transaksi.no_invoice as ref, transaksi.tanggal_transaksi as tanggal, CONCAT('Penjualan ke ', transaksi.nama_pembeli) as deskripsi, users.username as user, 'keluar' as icon")
            ->join('users', 'users.user_id = transaksi.id_user')
            ->orderBy('transaksi.tanggal_transaksi', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        // Gabungkan dan urutkan
        $activities = array_merge($pembelian, $penjualan);

        // Urutkan berdasarkan tanggal
        usort($activities, function ($a, $b) {
            return strtotime($b['tanggal']) - strtotime($a['tanggal']);
        });

        // Ambil $limit teratas
        return array_slice($activities, 0, $limit);
    }
}