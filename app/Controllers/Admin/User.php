<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class User extends BaseController
{
    protected $userModel;

    public function __construct()
    {
         // Cek role, hanya admin yang boleh akses
        $role = session()->get('role');
        if ($role !== 'admin') {
            // Redirect ke dashboard dengan pesan error
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $users = $this->userModel->orderBy('user_id', 'DESC')->findAll();
        
        $data = [
            'title' => 'Kelola Data User',
            'users' => $users
        ];
        
        return view('admin/user/index', $data);
    }

    public function store()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]|alpha_numeric',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'role' => 'required|in_list[admin,karyawan,pemilik]'
        ];
        
        $messages = [
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
            'confirm_password' => [
                'required' => 'Konfirmasi password wajib diisi.',
                'matches' => 'Konfirmasi password tidak sesuai.'
            ],
            'role' => [
                'required' => 'Role wajib dipilih.'
            ]
        ];
        
        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        try {
            $this->userModel->save([
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password'),
                'role' => $this->request->getPost('role'),
            ]);

            return redirect()->to('/admin/user')
                ->with('success', 'User berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function update($id)
    {
        // Cek apakah user ada
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'Data user tidak ditemukan.');
        }

        // Ambil data dari form
        $username = $this->request->getPost('username');
        $role = $this->request->getPost('role');
        $password = $this->request->getPost('password');
        $confirm_password = $this->request->getPost('confirm_password');
        
        // Siapkan data untuk update
        $updateData = [];
        $errors = [];
        
        // 1. Validasi Username (wajib)
        if (empty($username)) {
            $errors['username'] = 'Username wajib diisi.';
        } elseif (strlen($username) < 3) {
            $errors['username'] = 'Username minimal 3 karakter.';
        } elseif (strlen($username) > 50) {
            $errors['username'] = 'Username maksimal 50 karakter.';
        } elseif (!ctype_alnum($username)) {
            $errors['username'] = 'Username hanya boleh huruf dan angka.';
        } else {
            // Cek unique username (kecuali dirinya sendiri)
            $existing = $this->userModel->where('username', $username)->where('user_id !=', $id)->first();
            if ($existing) {
                $errors['username'] = 'Username sudah digunakan.';
            } else {
                $updateData['username'] = $username;
            }
        }
        
        // 2. Validasi Role (wajib)
        if (empty($role)) {
            $errors['role'] = 'Role wajib dipilih.';
        } elseif (!in_array($role, ['admin', 'karyawan'])) {
            $errors['role'] = 'Role tidak valid.';
        } else {
            // Cek jika mengubah role admin terakhir
            if ($user['role'] == 'admin' && $role != 'admin') {
                $adminCount = $this->userModel->where('role', 'admin')->where('user_id !=', $id)->countAllResults();
                if ($adminCount < 1) {
                    $errors['role'] = 'Tidak dapat mengubah role admin terakhir.';
                } else {
                    $updateData['role'] = $role;
                }
            } else {
                $updateData['role'] = $role;
            }
        }
        
        // 3. Validasi Password (opsional)
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors['password'] = 'Password minimal 6 karakter.';
            } elseif (empty($confirm_password)) {
                $errors['confirm_password'] = 'Konfirmasi password wajib diisi.';
            } elseif ($password !== $confirm_password) {
                $errors['confirm_password'] = 'Konfirmasi password tidak sesuai.';
            } else {
                $updateData['password'] = $password;
            }
        }
        
        // Jika ada error, kembalikan
        if (!empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $errors);
        }
        
        // Jika tidak ada data yang diupdate
        if (empty($updateData)) {
            return redirect()->back()
                ->with('info', 'Tidak ada perubahan yang disimpan.');
        }

        try {
            $this->userModel->update($id, $updateData);

            return redirect()->to('/admin/user')
                ->with('success', 'User berhasil diperbarui.');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'Data user tidak ditemukan.');
        }

        // Cek jangan sampai menghapus akun sendiri
        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Cek jika ini admin terakhir
        if ($user['role'] == 'admin') {
            $adminCount = $this->userModel->where('role', 'admin')->where('user_id !=', $id)->countAllResults();
            if ($adminCount < 1) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus admin terakhir.');
            }
        }

        try {
            $this->userModel->delete($id);
            return redirect()->to('/admin/user')->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}