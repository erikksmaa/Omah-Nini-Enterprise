<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userRole = session()->get('role');
        
        if (!$userRole) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }
        
        // Jika tidak ada arguments, izinkan semua
        if (empty($arguments)) {
            return;
        }
        
        $allowedRoles = $arguments;
        
        // Apakah role user ada di allowed roles?
        if (!in_array($userRole, $allowedRoles)) {
            // Redirect ke dashboard sesuai role atau ke halaman sebelumnya
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}