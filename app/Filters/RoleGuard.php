<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        $currentPath = $request->getPath();

        // Jika tidak ada arguments, allow semua (tidak mungkin terjadi karena filter selalu dipanggil dengan arguments)
        if (empty($arguments)) {
            return;
        }

        $allowedRoles = $arguments;

        // Cek apakah role user diizinkan untuk route group ini
        if (!in_array($role, $allowedRoles)) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        // ========== RESTRIKSI KHUSUS BERDASARKAN ROLE ==========

        // KARYAWAN: Tidak boleh akses halaman admin (master data, user, laporan)
        if ($role === 'karyawan') {
            $forbiddenPaths = [
                'admin/supplier',
                'admin/motif',
                'admin/warna',
                'admin/produk',
                'admin/pelanggan',
                'admin/user',
                'admin/laporan'
            ];

            foreach ($forbiddenPaths as $path) {
                if (strpos($currentPath, $path) === 0) {
                    return redirect()->to('/karyawan/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu melakukan apa-apa
    }
}