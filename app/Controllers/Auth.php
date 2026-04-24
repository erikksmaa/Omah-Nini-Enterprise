<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        // Jika sudah login, tendang kembali ke dashboard
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function process()
    {
        $users = new UserModel();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Validasi input tidak boleh kosong
        if (empty($username) || empty($password)) {
            session()->setFlashdata('error', 'Username dan password harus diisi!');
            return redirect()->back();
        }

        $dataUser = $users->where('username', $username)->first();

        if ($dataUser) {
            // Cek status user (jika ada kolom is_active)
            if (isset($dataUser['is_active']) && $dataUser['is_active'] == 0) {
                session()->setFlashdata('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.');
                return redirect()->back();
            }

            // Di controller Auth
            if (password_verify($password, $dataUser['password'])) {
                session()->set([
                    'user_id' => $dataUser['user_id'],
                    'username' => $dataUser['username'],
                    'role' => $dataUser['role'],
                    'logged_in' => true,
                    'login_message' => 'Selamat datang, ' . $dataUser['username'] . '!'  // ← TAMBAHKAN
                ]);

                return redirect()->to('/dashboard');

            } else {
                session()->setFlashdata('error', 'Password yang Anda masukkan salah.');
                return redirect()->back();
            }
        } else {
            session()->setFlashdata('error', 'Username tidak ditemukan.');
            return redirect()->back();
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login?logout=success');
    }
}