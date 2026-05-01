<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'no_invoice',
        'id_user',
        'tanggal_transaksi',
        'total_bayar',
        'tipe_pembayaran',
        'status',
        'catatan',
        'created_at'
    ];

    // Matikan auto timestamps agar kita bisa set manual
    protected $useTimestamps = false;

    /**
 * Get weekly transaksi count (last 7 days)
 */
public function getWeeklyCount()
{
    $result = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $count = $this->where('DATE(tanggal_transaksi)', $date)
                      ->countAllResults();
        $result[] = [
            'date' => $date,
            'total' => $count
        ];
    }
    return $result;
}

/**
 * Get count transaksi this month
 */
public function getCountTransactionsThisMonth()
{
    return $this->where('MONTH(tanggal_transaksi)', date('m'))
                ->where('YEAR(tanggal_transaksi)', date('Y'))
                ->countAllResults();
}

/**
 * Get count transaksi today
 */
public function getCountTransactionsToday()
{
    return $this->where('DATE(tanggal_transaksi)', date('Y-m-d'))
                ->countAllResults();
}
}

