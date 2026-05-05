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
            
            case 'karyawan':
                return redirect()->to('/karyawan/dashboard');
            
            case 'owner':
                return redirect()->to('/pemilik/dashboard');
            
            default:
                // Jika role tidak dikenal, logout
                session()->destroy();
                return redirect()->to('/login')->with('error', 'Role tidak dikenal!');
        }
    }
}