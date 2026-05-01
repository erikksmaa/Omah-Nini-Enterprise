<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // Cek apakah sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');

        // Redirect berdasarkan role
        switch ($role) {
            case 'admin':
                return redirect()->to('/admin/dashboard');
            case 'gudang':
                return redirect()->to('/gudang/dashboard');
            case 'kasir':
                return redirect()->to('/kasir/dashboard');
            default:
                // Jika role tidak dikenal, logout
                session()->destroy();
                return redirect()->to('/login')->with('error', 'Role tidak dikenal!');
        }
    }
}