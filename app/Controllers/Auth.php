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

        $dataUser = $users->where('username', $username)->first();

        if ($dataUser) {
            // Evaluasi password menggunakan algoritma bcrypt
            if (password_verify($password, $dataUser['password'])) {
                session()->set([
                    'user_id' => $dataUser['user_id'],
                    'username' => $dataUser['username'],
                    'role' => $dataUser['role'],
                    'logged_in' => true
                ]);
                return redirect()->to('/dashboard');
            } else {
                session()->setFlashdata('error', 'Password salah.');
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
        return redirect()->to('/login');
    }
}