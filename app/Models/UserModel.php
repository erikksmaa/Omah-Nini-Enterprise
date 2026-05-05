<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['username', 'password', 'role'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Hapus validation rules dari sini (pindahkan ke controller)
    // agar lebih fleksibel

    // Auto hash password
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        } elseif (isset($data['data']['password'])) {
            unset($data['data']['password']);
        }
        return $data;
    }

    // ========== CUSTOM METHODS ==========

    public function verifyLogin($username, $password)
    {
        $user = $this->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    public function getAllPaginated($perPage = 10)
    {
        return $this->orderBy('user_id', 'DESC')->paginate($perPage);
    }

    public function getById($id)
    {
        return $this->find($id);
    }

    public function getByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function countByRole($role)
    {
        return $this->where('role', $role)->countAllResults();
    }

    public function isLastAdmin($id)
    {
        $user = $this->getById($id);
        if (!$user || $user['role'] != 'admin') {
            return false;
        }
        $adminCount = $this->where('role', 'admin')->where('user_id !=', $id)->countAllResults();
        return $adminCount < 1;
    }

    public function getOptions()
    {
        return $this->select('user_id, username, role')->orderBy('username', 'ASC')->findAll();
    }

    public function search($keyword, $limit = 10)
    {
        return $this->groupStart()
            ->like('username', $keyword)
            ->orLike('role', $keyword)
            ->groupEnd()
            ->limit($limit)
            ->findAll();
    }

    public function getByRole($role)
    {
        return $this->where('role', $role)->orderBy('username', 'ASC')->findAll();
    }

    public function getTotalUser()
    {
        return $this->countAllResults();
    }
}