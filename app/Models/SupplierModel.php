<?php

namespace App\Models;

use CodeIgniter\Model;

class SupplierModel extends Model
{
    protected $table = 'supplier';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'kontak', 'email', 'alamat'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField     = ''; // Suppier jarang diupdate

}
