<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class User extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $users = $this->userModel->getAllPaginated(10);
        
        $data = [
            'title' => 'Kelola Data User',
            'users' => $users,
            'pager' => $this->userModel->pager
        ];
        
        return view('admin/user/index', $data);
    }

    public function store()
    {
        $rules = [
            'username' => [
                'rules' => 'required|min_length[3]|max_length[50]|is_unique[users.username]|alpha_numeric',
                'errors' => [
                    'required' => 'Username wajib diisi.',
                    'min_length' => 'Username minimal 3 karakter.',
                    'max_length' => 'Username maksimal 50 karakter.',
                    'is_unique' => 'Username sudah digunakan.',
                    'alpha_numeric' => 'Username hanya boleh huruf dan angka.'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password wajib diisi.',
                    'min_length' => 'Password minimal 6 karakter.'
                ]
            ],
            'confirm_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi password wajib diisi.',
                    'matches' => 'Konfirmasi password tidak sesuai.'
                ]
            ],
            'role' => [
                'rules' => 'required|in_list[admin,gudang,kasir]',
                'errors' => [
                    'required' => 'Role wajib dipilih.',
                    'in_list' => 'Role tidak valid.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        try {
            $this->userModel->save([
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password'), // Akan di-hash otomatis oleh model
                'role' => $this->request->getPost('role')
            ]);

            return redirect()->to('/admin/user')->with('success', 'User berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        $user = $this->userModel->getById($id);
        if (!$user) {
            return redirect()->to('/admin/user')->with('error', 'User tidak ditemukan.');
        }

        $rules = [
            'username' => [
                'rules' => "required|min_length[3]|max_length[50]|alpha_numeric|is_unique[users.username,user_id,{$id}]",
                'errors' => [
                    'required' => 'Username wajib diisi.',
                    'min_length' => 'Username minimal 3 karakter.',
                    'max_length' => 'Username maksimal 50 karakter.',
                    'alpha_numeric' => 'Username hanya huruf dan angka.',
                    'is_unique' => 'Username sudah digunakan user lain.'
                ]
            ],
            'role' => [
                'rules' => 'required|in_list[admin,gudang,kasir]',
                'errors' => [
                    'required' => 'Role wajib dipilih.',
                    'in_list' => 'Role tidak valid.'
                ]
            ]
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = [
                'rules' => 'min_length[6]',
                'errors' => ['min_length' => 'Password minimal 6 karakter.']
            ];
            $rules['confirm_password'] = [
                'rules' => 'matches[password]',
                'errors' => ['matches' => 'Konfirmasi password tidak sesuai.']
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
        }

        $dataUpdate = [
            'username' => $this->request->getPost('username'),
            'role' => $this->request->getPost('role')
        ];

        if (!empty($password)) {
            $dataUpdate['password'] = $password; // Akan di-hash otomatis oleh model
        }

        try {
            $this->userModel->update($id, $dataUpdate);
            return redirect()->to('/admin/user')->with('success', 'User berhasil diperbarui.');
            
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $user = $this->userModel->getById($id);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        // Cek apakah menghapus akun sendiri
        if (session()->get('user_id') == $id) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        // Cek apakah admin terakhir
        if ($this->userModel->isLastAdmin($id)) {
            return redirect()->back()->with('error', 'Minimal harus ada 1 admin.');
        }

        try {
            $this->userModel->delete($id);
            return redirect()->back()->with('success', 'User berhasil dihapus.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}