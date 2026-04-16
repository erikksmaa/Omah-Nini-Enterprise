<?php
namespace App\Models;

use CodeIgniter\Model;

class PembelianModel extends Model
{
    protected $table = 'pembelian';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    // Hanya kolom yang boleh diisi
    protected $allowedFields = [
        'no_invoice',
        'id_supplier',
        'id_user',
        'tanggal_pembelian',
        'total_harga',
        'catatan'
    ];

    // Matikan timestamps otomatis karena kita akan set manual
    protected $useTimestamps = false;
    protected $validationRules = [
        'no_invoice' => 'required|is_unique[pembelian.no_invoice]',
        'id_supplier' => 'required|is_not_unique[supplier.id]',
        'id_user' => 'required|is_not_unique[users.user_id]',
        'total_harga' => 'required|numeric|greater_than[0]'
    ];

    public function generateInvoice()
    {
        $last = $this->orderBy('id', 'DESC')->first();
        $lastNumber = $last ? intval(substr($last['no_invoice'], -4)) : 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        return 'PO-' . date('Ymd') . '-' . $newNumber;
    }

    /**
     * Get pembelian dengan detail supplier
     */
    public function getWithSupplier($limit = null)
    {
        $builder = $this->select('pembelian.*, supplier.nama as supplier_nama, users.username')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->join('users', 'users.user_id = pembelian.id_user', 'left')
            ->orderBy('pembelian.id', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get total pembelian bulan ini
     */
    public function getTotalPembelianBulanIni()
    {
        return $this->selectSum('total_harga')
            ->where('MONTH(tanggal_pembelian)', date('m'))
            ->where('YEAR(tanggal_pembelian)', date('Y'))
            ->first()['total_harga'] ?? 0;
    }

    /**
     * Get count pembelian bulan ini
     */
    public function getCountPembelianBulanIni()
    {
        return $this->where('MONTH(tanggal_pembelian)', date('m'))
            ->where('YEAR(tanggal_pembelian)', date('Y'))
            ->countAllResults();
    }

    /**
     * Get pembelian by ID dengan detail
     */
    public function getById($id)
    {
        return $this->select('pembelian.*, supplier.nama as supplier_nama, users.username')
            ->join('supplier', 'supplier.id = pembelian.id_supplier')
            ->join('users', 'users.user_id = pembelian.id_user', 'left')
            ->find($id);
    }
}