<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DashboardAdminModel;

class Dashboard extends BaseController
{
    protected $dashboardModel;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            redirect()->to('/login');
        }

        $this->dashboardModel = new DashboardAdminModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard Admin',
            'username' => session()->get('username'),
            'role' => session()->get('role'),
            'total_produk' => $this->dashboardModel->getTotalProduk(),
            'total_kategori' => $this->dashboardModel->getTotalKategori(),
            'total_supplier' => $this->dashboardModel->getTotalSupplier(),
            'total_user' => $this->dashboardModel->getTotalUser(),
            'stok_menipis' => $this->dashboardModel->getStokMenipis(),
            'stok_habis' => $this->dashboardModel->getStokHabis(),
        ];

        return view('admin/dashboard', $data);
    }

    // API untuk Chart Penjualan 7 Hari Terakhir
    public function getWeeklySalesChart()
    {
        $data = $this->dashboardModel->getWeeklySales();
        return $this->response->setJSON($data);
    }

    // API untuk Chart Penjualan 12 Bulan Terakhir
    public function getMonthlySalesChart()
    {
        $data = $this->dashboardModel->getMonthlySales();
        return $this->response->setJSON($data);
    }

    // API untuk Chart Laba/Rugi Bulan Ini
    public function getProfitLossChart()
    {
        $data = $this->dashboardModel->getProfitLoss();
        return $this->response->setJSON($data);
    }

    // API untuk Data Stok Menipis
    public function getLowStockData()
    {
        $data = $this->dashboardModel->getLowStockProducts(10);
        return $this->response->setJSON($data);
    }

    // API untuk Produk Terlaris
    public function getTopProductsChart()
    {
        $data = $this->dashboardModel->getTopProducts();
        return $this->response->setJSON($data);
    }

    // API untuk Tipe Pembayaran
    public function getPaymentMethodChart()
    {
        $data = $this->dashboardModel->getPaymentMethods();
        return $this->response->setJSON($data);
    }

    // API untuk Data Dashboard Lengkap
    public function getDashboardData()
    {
        $data = $this->dashboardModel->getDashboardData();
        return $this->response->setJSON($data);
    }
}