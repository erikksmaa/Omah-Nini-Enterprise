<?php
namespace App\Models;

use CodeIgniter\Model;

class LogStokModel extends Model
{
    protected $table = 'log_stok';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id_produk',
        'id_user',
        'tipe_ref',
        'id_ref',
        'jumlah_sebelum',
        'jumlah_perubahan',
        'jumlah_sesudah',
        'created_at'
    ];

    // Method tambahan bisa ditambahkan nanti untuk laporan
    // ========== METHOD UNTUK LAPORAN LOG STOK ==========

    /**
     * Get log stok with filters
     */
    public function getLogStokReport($start_date = null, $end_date = null, $id_produk = null, $tipe_ref = null)
    {
        $builder = $this->select('log_stok.*, users.username, produk.sku, motif.nama_motif, warna.nama_warna, supplier.nama as nama_supplier')
            ->join('users', 'users.user_id = log_stok.id_user')
            ->join('produk', 'produk.id = log_stok.id_produk')
            ->join('motif', 'motif.id = produk.id_motif')
            ->join('warna', 'warna.id = produk.id_warna')
            ->join('supplier', 'supplier.id = produk.id_supplier')
            ->orderBy('log_stok.id', 'DESC');

        if (!empty($start_date) && !empty($end_date)) {
            $builder->where('DATE(log_stok.created_at) >=', $start_date)
                ->where('DATE(log_stok.created_at) <=', $end_date);
        }

        if (!empty($id_produk)) {
            $builder->where('log_stok.id_produk', $id_produk);
        }

        if (!empty($tipe_ref)) {
            $builder->where('log_stok.tipe_ref', $tipe_ref);
        }

        return $builder->findAll();
    }

    /**
     * Get summary perubahan stok per tipe
     */
    public function getSummaryPerTipe($start_date = null, $end_date = null)
    {
        $builder = $this->select('
        tipe_ref,
        SUM(jumlah_perubahan) as total_perubahan,
        COUNT(*) as jumlah_transaksi
    ');

        if (!empty($start_date) && !empty($end_date)) {
            $builder->where('DATE(created_at) >=', $start_date)
                ->where('DATE(created_at) <=', $end_date);
        }

        return $builder->groupBy('tipe_ref')->findAll();
    }

    
}