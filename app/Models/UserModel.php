<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id'; // Ubah dari id menjadi user_id sesuai dokumen A3 
    protected $useAutoIncrement = true;
    protected $allowedFields = ['username', 'password', 'role'];

    // Aktifkan ini agar created_at dan updated_at diatur otomatis oleh CI4
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
