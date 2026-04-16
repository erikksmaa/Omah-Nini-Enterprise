<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
 public function index()
    {
        // Cek apakah sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Dashboard',
            'username' => session()->get('username'),
            'role' => session()->get('role')
        ];

        return view('dashboard', $data);
    }

    // Endpoint tiruan untuk AJAX Chart (Nanti kita hubungkan ke Database)
    public function getChartData()
    {
        return $this->response->setJSON([
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
            'values' => [1500000, 2300000, 1800000, 3200000, 2900000, 4500000]
        ]);
    }

    public function getDailyPieChartData()
    {
        return $this->response->setJSON([
            'labels' => ['Cash', 'Transfer', 'Piutang'],
            'values' => [500000, 1200000, 300000]
        ]);
    }

    public function getDailyBarChartData()
    {
        return $this->response->setJSON([
            'labels' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            'quantities' => [12, 19, 15, 25, 22, 30, 45],
            'revenues' => [1200000, 1900000, 1500000, 2500000, 2200000, 3000000, 4500000]
        ]);
    }

    public function getTipeBarangOptions()
    {
        return $this->response->setJSON([
            ['id_tipe' => 'Mainan Edukasi'],
            ['id_tipe' => 'Mobil RC'],
            ['id_tipe' => 'Action Figure']
        ]);
    }

    public function checkStandarHargaJualStatus()
    {
        return $this->response->setJSON([
            'is_updated' => true,
            'last_update_date' => date('Y-m-d'),
            'days_since_update' => 0
        ]);
    }
}

