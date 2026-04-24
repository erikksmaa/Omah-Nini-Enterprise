<?php
namespace App\Controllers\Gudang;

use App\Controllers\BaseController;
use App\Models\DashboardGudangModel;

class Dashboard extends BaseController
{
    protected $dashboardModel;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/login');
        }
        
        $role = session()->get('role');
        if ($role != 'gudang' && $role != 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }
        
        $this->dashboardModel = new DashboardGudangModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard Gudang',
            'username' => session()->get('username'),
            'role' => session()->get('role'),
            'total_produk' => $this->dashboardModel->getTotalProduk(),
            'stok_menipis' => $this->dashboardModel->getStokMenipis(),
            'stok_habis' => $this->dashboardModel->getStokHabis(),
            'total_pembelian_bulan_ini' => $this->dashboardModel->getTotalPembelianBulanIni(),
            'jumlah_pembelian_bulan_ini' => $this->dashboardModel->getJumlahPembelianBulanIni(),
            'pembelian_terbaru' => $this->dashboardModel->getPembelianTerbaru(5),
            'produk_terbanyak' => $this->dashboardModel->getProdukStokTerbanyak(5),
            'produk_menipis' => $this->dashboardModel->getProdukStokMenipis(10),
            'log_terbaru' => $this->dashboardModel->getLogStokTerbaru(10),
            'total_nilai_stok' => $this->dashboardModel->getTotalNilaiStok(),
            'statistik_supplier' => $this->dashboardModel->getStatistikSupplier(5)
        ];
        
        return view('gudang/dashboard', $data);
    }
}