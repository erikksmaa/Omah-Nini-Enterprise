<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model {
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';  // ← PERBAIKAN
    protected $updatedField = 'updated_at';
    
    protected $allowedFields = [
        'username',
        'password',
        'role'
    ];
}