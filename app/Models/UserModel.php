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

    // ========== VALIDATION RULES ==========
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]|alpha_numeric',
        'password' => 'required|min_length[6]',
        'role' => 'required|in_list[admin,gudang,kasir]'
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'Username wajib diisi.',
            'min_length' => 'Username minimal 3 karakter.',
            'max_length' => 'Username maksimal 50 karakter.',
            'is_unique' => 'Username sudah digunakan.',
            'alpha_numeric' => 'Username hanya boleh huruf dan angka.'
        ],
        'password' => [
            'required' => 'Password wajib diisi.',
            'min_length' => 'Password minimal 6 karakter.'
        ],
        'role' => [
            'required' => 'Role wajib dipilih.',
            'in_list' => 'Role tidak valid.'
        ]
    ];

    // ========== AUTO HASH PASSWORD ==========
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    // ========== CUSTOM METHODS ==========

    /**
     * Get all users with pagination
     */
    public function getAllPaginated($perPage = 10)
    {
        return $this->orderBy('user_id', 'DESC')->paginate($perPage);
    }

    /**
     * Get user by ID
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * Get user by username
     */
    public function getByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Update user without password (for updates where password not changed)
     */
    public function updateWithoutPassword($id, $data)
    {
        // Remove password from data if empty
        if (empty($data['password'])) {
            unset($data['password']);
        }
        return $this->update($id, $data);
    }

    /**
     * Count users by role
     */
    public function countByRole($role)
    {
        return $this->where('role', $role)->countAllResults();
    }

    /**
     * Check if is last admin
     */
    public function isLastAdmin($id)
    {
        $user = $this->getById($id);
        if ($user['role'] != 'admin') {
            return false;
        }
        $adminCount = $this->countByRole('admin');
        return $adminCount <= 1;
    }

    /**
     * Get all users for dropdown/select options
     */
    public function getOptions()
    {
        return $this->select('user_id, username, role')->orderBy('username', 'ASC')->findAll();
    }

    /**
     * Search users by username or role
     */
    public function search($keyword, $limit = 10)
    {
        return $this->groupStart()
            ->like('username', $keyword)
            ->orLike('role', $keyword)
            ->groupEnd()
            ->limit($limit)
            ->findAll();
    }
}