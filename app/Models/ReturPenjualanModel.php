<?php

namespace App\Models;

use CodeIgniter\Model;

class ReturPenjualanModel extends Model
{
    protected $table = 'retur_penjualan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $createdField = 'created_at';
    protected $updatedField = null;
    
    protected $allowedFields = [
        'no_retur',
        'id_transaksi',
        'tanggal_retur',
        'total_retur',
        'alasan',
        'id_user',
        'created_at'
    ];
    
    // ========== GENERATE NO RETUR ==========
    public function generateNoRetur()
    {
        $last = $this->orderBy('id', 'DESC')->first();
        if ($last && isset($last['no_retur'])) {
            $lastNumber = (int) substr($last['no_retur'], -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        return 'RET-' . date('ymd') . '-' . $newNumber;
    }
    
    // ========== GET METHODS ==========
    
    /**
     * Get all retur with transaksi and user info
     */
    public function getAllWithTransaksi()
    {
        return $this->select('retur_penjualan.*, transaksi.no_invoice, users.username')
            ->join('transaksi', 'transaksi.id = retur_penjualan.id_transaksi')
            ->join('users', 'users.user_id = retur_penjualan.id_user', 'left')
            ->orderBy('retur_penjualan.id', 'DESC')
            ->findAll();
    }
    
    /**
     * Get retur with pagination and filters
     */
    public function getWithTransaksi($perPage = 10, $keyword = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('retur_penjualan.*, transaksi.no_invoice, users.username')
            ->join('transaksi', 'transaksi.id = retur_penjualan.id_transaksi')
            ->join('users', 'users.user_id = retur_penjualan.id_user', 'left');
        
        // Filter pencarian
        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('retur_penjualan.no_retur', $keyword)
                ->orLike('transaksi.no_invoice', $keyword)
                ->groupEnd();
        }
        
        // Filter tanggal
        if (!empty($startDate)) {
            $builder->where('retur_penjualan.tanggal_retur >=', $startDate);
        }
        if (!empty($endDate)) {
            $builder->where('retur_penjualan.tanggal_retur <=', $endDate);
        }
        
        return $builder->orderBy('retur_penjualan.id', 'DESC')->paginate($perPage);
    }
    
    /**
     * Get retur by ID with transaksi info
     */
    public function getById($id)
    {
        return $this->select('retur_penjualan.*, transaksi.no_invoice, users.username')
            ->join('transaksi', 'transaksi.id = retur_penjualan.id_transaksi')
            ->join('users', 'users.user_id = retur_penjualan.id_user', 'left')
            ->find($id);
    }
    
    /**
     * Get available transactions for retur (selesai & belum diretur)
     */
    public function getAvailableTransactionsForRetur()
    {
        $db = \Config\Database::connect();
        return $db->table('transaksi')
            ->select('transaksi.*, users.username')
            ->join('users', 'users.user_id = transaksi.id_user', 'left')
            ->where('transaksi.status', 'selesai')
            ->whereNotIn('transaksi.id', function($builder) {
                $builder->select('id_transaksi')->from('retur_penjualan');
            })
            ->orderBy('transaksi.id', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get retur report by date range
     */
    public function getReturReport($startDate, $endDate, $status = null)
    {
        $builder = $this->select('retur_penjualan.*, transaksi.no_invoice, users.username')
            ->join('transaksi', 'transaksi.id = retur_penjualan.id_transaksi')
            ->join('users', 'users.user_id = retur_penjualan.id_user', 'left')
            ->where('retur_penjualan.tanggal_retur >=', $startDate)
            ->where('retur_penjualan.tanggal_retur <=', $endDate);
        
        if ($status) {
            $builder->where('retur_penjualan.status', $status);
        }
        
        return $builder->orderBy('retur_penjualan.tanggal_retur', 'DESC')->findAll();
    }
    
    /**
     * Get retur statistics
     */
    public function getReturStatistics($startDate, $endDate)
    {
        $retur = $this->getReturReport($startDate, $endDate);
        $totalRetur = count($retur);
        $totalNominalRetur = array_sum(array_column($retur, 'total_retur'));
        $rataRetur = $totalRetur > 0 ? $totalNominalRetur / $totalRetur : 0;
        
        return [
            'total_retur' => $totalRetur,
            'total_nominal' => $totalNominalRetur,
            'rata_rata' => $rataRetur
        ];
    }
    
    /**
     * Get top products returned
     */
    public function getTopProductsReturned($limit = 10)
    {
        $db = \Config\Database::connect();
        return $db->table('detail_retur_penjualan')
            ->select('detail_retur_penjualan.nama_produk, SUM(detail_retur_penjualan.jumlah) as total_jumlah, SUM(detail_retur_penjualan.subtotal) as total_nominal')
            ->groupBy('detail_retur_penjualan.nama_produk')
            ->orderBy('total_jumlah', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get retur per month
     */
    public function getReturPerMonth()
    {
        $db = \Config\Database::connect();
        return $db->table('retur_penjualan')
            ->select('DATE_FORMAT(tanggal_retur, "%Y-%m") as bulan, COUNT(*) as jumlah, SUM(total_retur) as total')
            ->groupBy('DATE_FORMAT(tanggal_retur, "%Y-%m")')
            ->orderBy('bulan', 'DESC')
            ->get()
            ->getResultArray();
    }
    
    /**
     * Get top reasons for return
     */
    public function getTopReasons($limit = 5)
    {
        $db = \Config\Database::connect();
        return $db->table('retur_penjualan')
            ->select('alasan, COUNT(*) as jumlah, SUM(total_retur) as total')
            ->groupBy('alasan')
            ->orderBy('jumlah', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}