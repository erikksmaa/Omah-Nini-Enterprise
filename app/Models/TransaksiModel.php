<?php
namespace App\Models;

use CodeIgniter\Model;

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
    public function generateNoInvoice()
    {
        $last = $this->orderBy('id', 'DESC')->first();
        if ($last && isset($last['no_invoice'])) {
            $lastNumber = (int) substr($last['no_invoice'], -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        return 'INV-' . date('ymd') . '-' . $newNumber;
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
            // 1. Insert header transaksi
            $db->table('transaksi')->insert($headerData);
            $transaksiId = $db->insertID();

            foreach ($items as $item) {
                // 2. Insert detail transaksi
                $db->table('detail_transaksi')->insert([
                    'id_transaksi' => $transaksiId,
                    'id_produk'    => $item['id_produk'],
                    'nama_produk'  => $item['nama_produk'],
                    'jumlah'       => $item['jumlah']
                ]);

                // 3. Ambil stok sekarang
                $produk = $db->table('produk')->where('id', $item['id_produk'])->get()->getRowArray();
                if (!$produk) {
                    throw new \Exception("Produk dengan ID {$item['id_produk']} tidak ditemukan.");
                }

                $stokLama = $produk['stok'];
                if ($stokLama < $item['jumlah']) {
                    throw new \Exception("Stok tidak mencukupi untuk produk {$item['nama_produk']}. Stok tersedia: {$stokLama}, diminta: {$item['jumlah']}");
                }

                $stokBaru = $stokLama - $item['jumlah'];

                // 4. Update stok
                $db->table('produk')->where('id', $item['id_produk'])->update(['stok' => $stokBaru]);

                // 5. Catat log stok
                $db->table('log_stok')->insert([
                    'id_produk'        => $item['id_produk'],
                    'id_user'          => $userId,
                    'tipe_ref'         => 'penjualan',
                    'id_ref'           => $transaksiId,
                    'jumlah_sebelum'   => $stokLama,
                    'jumlah_perubahan' => $item['jumlah'],
                    'jumlah_sesudah'   => $stokBaru,
                    'created_at'       => $now
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi gagal disimpan.');
            }

            return ['success' => true, 'id' => $transaksiId];

        } catch (\Exception $e) {
            $db->transRollback();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}