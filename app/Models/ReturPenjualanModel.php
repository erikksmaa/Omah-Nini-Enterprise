<?php

namespace App\Models;

use CodeIgniter\Model;

class ReturPenjualanModel extends Model
{
    protected $table = 'retur_penjualan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;
    
   // app/Models/ReturPenjualanModel.php
protected $allowedFields = [
    'no_retur',
    'id_transaksi',
    'tanggal_retur',
    'total_retur',
    'alasan',
    'id_user'
];
    
    // Generate nomor retur otomatis
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
    
    // Get retur dengan detail transaksi
    public function getWithTransaksi()
    {
        return $this->select('retur_penjualan.*, transaksi.no_invoice, users.username')
                    ->join('transaksi', 'transaksi.id = retur_penjualan.id_transaksi')
                    ->join('users', 'users.user_id = retur_penjualan.id_user', 'left')
                    ->orderBy('retur_penjualan.id', 'DESC')
                    ->findAll();
    }
    
    // Get retur by ID dengan detail
    public function getById($id)
    {
        return $this->select('retur_penjualan.*, transaksi.no_invoice, users.username')
                    ->join('transaksi', 'transaksi.id = retur_penjualan.id_transaksi')
                    ->join('users', 'users.user_id = retur_penjualan.id_user', 'left')
                    ->find($id);
    }
}