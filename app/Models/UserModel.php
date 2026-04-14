<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model {
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $useTimestamps = false;
    // protected $createdField = 'created_at';
    // protected $updatedField = 'updated_at';
    
    protected $allowedFields = [
        'username',
        'password',
        'role',

    ];
}