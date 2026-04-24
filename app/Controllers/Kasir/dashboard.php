<?php
namespace App\Controllers\Kasir;

use App\Controllers\BaseController;
use App\Models\DashboardKasirModel;

class Dashboard extends BaseController
{
    protected $dashboardModel;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/login');
        }
        
        $role = session()->get('role');
        if ($role != 'kasir' && $role != 'admin') {
            redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }
        
        $this->dashboardModel = new DashboardKasirModel();
    }

    public function index()
    {
        $penjualanHariIni = $this->dashboardModel->getPenjualanHariIni();
        $penjualanBulanIni = $this->dashboardModel->getPenjualanBulanIni();
        $rataPenjualan = $this->dashboardModel->getRataPenjualanHarian();
        
        $data = [
            'title' => 'Dashboard Kasir',
            'username' => session()->get('username'),
            'role' => session()->get('role'),
            'jumlah_transaksi_hari_ini' => $penjualanHariIni['jumlah'],
            'omset_hari_ini' => $penjualanHariIni['total'],
            'jumlah_transaksi_bulan_ini' => $penjualanBulanIni['jumlah'],
            'omset_bulan_ini' => $penjualanBulanIni['total'],
            'rata_transaksi_harian' => $rataPenjualan['rata_transaksi'],
            'rata_omset_harian' => $rataPenjualan['rata_omset'],
            'transaksi_terbaru' => $this->dashboardModel->getTransaksiTerbaru(10),
            'produk_terlaris_hari_ini' => $this->dashboardModel->getProdukTerlarisHariIni(5),
            'produk_terlaris_bulan_ini' => $this->dashboardModel->getProdukTerlarisBulanIni(5),
            'metode_pembayaran' => $this->dashboardModel->getMetodePembayaranHariIni(),
            'jam_sibuk' => $this->dashboardModel->getJamSibuk(),
            'stok_menipis' => $this->dashboardModel->getStokMenipis(10)
        ];
        
        return view('kasir/dashboard', $data);
    }
    
    // API untuk Chart Penjualan 7 Hari Terakhir
    public function getWeeklySalesChart()
    {
        $data = $this->dashboardModel->getWeeklySalesChart();
        return $this->response->setJSON($data);
    }
    
    // API untuk Chart Metode Pembayaran
    public function getPaymentMethodChart()
    {
        $data = $this->dashboardModel->getPaymentMethodChart();
        return $this->response->setJSON($data);
    }
}