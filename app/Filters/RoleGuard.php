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
        
        // Jika tidak ada arguments, allow semua
        if (empty($arguments)) {
            return;
        }
        
        $allowedRoles = $arguments;
        
        // Cek apakah role user diizinkan
        if (!in_array($role, $allowedRoles)) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }
        
        // ========== RESTRIKSI KHUSUS ==========
        
        // GUDANG: Tidak boleh akses master data & kasir
        if ($role === 'gudang') {
            $forbiddenPaths = [
                'admin/kategori', 'admin/supplier', 'admin/produk', 'admin/user',
                'kasir/penjualan', 'admin/laporan/keuangan', 'admin/retur'
            ];
            foreach ($forbiddenPaths as $path) {
                if (strpos($currentPath, $path) === 0) {
                    return redirect()->to('/gudang/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
                }
            }
        }
        
        // KASIR: Tidak boleh akses master data, gudang, laporan
        if ($role === 'kasir') {
            $forbiddenPaths = [
                'admin/kategori', 'admin/supplier', 'admin/produk', 'admin/user',
                'gudang/pembelian', 'gudang/stok', 'admin/laporan', 'admin/retur'
            ];
            foreach ($forbiddenPaths as $path) {
                if (strpos($currentPath, $path) === 0) {
                    return redirect()->to('/kasir/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu melakukan apa-apa
    }
}