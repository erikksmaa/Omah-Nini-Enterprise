<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ========== ROUTE TANPA AUTH ==========
$routes->get('/', function () {
    if (session()->get('logged_in')) {
        $role = session()->get('role');
        return redirect()->to("/{$role}/dashboard");
    }
    return redirect()->to('/login');
});

$routes->get('/login', 'Auth::index');
$routes->post('/login/process', 'Auth::process');
$routes->get('/logout', 'Auth::logout');

// ========== ROUTE DENGAN AUTH ==========
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // Redirect dashboard berdasarkan role
    $routes->get('/dashboard', function () {
        $role = session()->get('role');
        return redirect()->to("/{$role}/dashboard");
    });

    // ========== ADMIN ROUTES (Hanya Admin) ==========
    $routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
        
        // Dashboard
        $routes->get('dashboard', 'Dashboard::index');
        
        // Master Data - Kategori
        $routes->get('kategori', 'Kategori::index');
        $routes->post('kategori/store', 'Kategori::store');
        $routes->post('kategori/update/(:num)', 'Kategori::update/$1');
        $routes->get('kategori/delete/(:num)', 'Kategori::delete/$1');
        
        // Master Data - Supplier
        $routes->get('supplier', 'Supplier::index');
        $routes->post('supplier/store', 'Supplier::store');
        $routes->post('supplier/update/(:num)', 'Supplier::update/$1');
        $routes->get('supplier/delete/(:num)', 'Supplier::delete/$1');
        
        // Master Data - Produk
        $routes->get('produk', 'Produk::index');
        $routes->post('produk/store', 'Produk::store');
        $routes->post('produk/update/(:num)', 'Produk::update/$1');
        $routes->get('produk/delete/(:num)', 'Produk::delete/$1');
        
        // Manajemen User
        $routes->get('user', 'User::index');
        $routes->post('user/store', 'User::store');
        $routes->post('user/update/(:num)', 'User::update/$1');
        $routes->get('user/delete/(:num)', 'User::delete/$1');
        
        // Laporan
        $routes->get('laporan', 'Laporan::index');
        $routes->get('laporan/keuangan', 'Laporan::keuangan');
        $routes->get('laporan/penjualan', 'Laporan::penjualan');
        $routes->get('laporan/pembelian', 'Laporan::pembelian');
        $routes->get('laporan/laba-rugi', 'Laporan::labaRugi');
        $routes->get('laporan/log-stok', 'Laporan::logStok');
        $routes->get('laporan/produk', 'Laporan::produk');
        
        // Export Laporan
        $routes->get('laporan/export-penjualan', 'Laporan::exportPenjualan');
        $routes->get('laporan/export-pembelian', 'Laporan::exportPembelian');
        $routes->get('laporan/export-keuangan', 'Laporan::exportKeuangan');
        $routes->get('laporan/export-laba-rugi', 'Laporan::exportLabaRugi');
        $routes->get('laporan/export-stok', 'Laporan::exportStok');
        
        // Retur Penjualan
        $routes->get('retur', 'Retur::index');
        $routes->get('retur/create', 'Retur::create');
        $routes->post('retur/store', 'Retur::store');
        $routes->get('retur/detail/(:num)', 'Retur::detail/$1');
        $routes->get('retur/delete/(:num)', 'Retur::delete/$1');
        $routes->get('retur/laporan', 'Retur::laporan');
        $routes->get('retur/export-excel', 'Retur::exportExcel');
        $routes->get('retur/getDetailTransaksi/(:num)', 'Retur::getDetailTransaksi/$1');
        
        // Admin Dashboard API
        $routes->group('dashboard', function ($routes) {
            $routes->get('getWeeklySalesChart', 'Dashboard::getWeeklySalesChart');
            $routes->get('getMonthlySalesChart', 'Dashboard::getMonthlySalesChart');
            $routes->get('getProfitLossChart', 'Dashboard::getProfitLossChart');
            $routes->get('getLowStockData', 'Dashboard::getLowStockData');
            $routes->get('getTopProductsChart', 'Dashboard::getTopProductsChart');
            $routes->get('getPaymentMethodChart', 'Dashboard::getPaymentMethodChart');
            $routes->get('getDashboardData', 'Dashboard::getDashboardData');
        });
    });

    // ========== GUDANG ROUTES (Gudang & Admin bisa akses) ==========
    $routes->group('gudang', ['namespace' => 'App\Controllers\Gudang'], function ($routes) {
        
        // Dashboard
        $routes->get('dashboard', 'Dashboard::index');
        
        // Pembelian
        $routes->get('pembelian', 'Pembelian::index');
        $routes->get('pembelian/create', 'Pembelian::create');
        $routes->post('pembelian/store', 'Pembelian::store');
        $routes->get('pembelian/detail/(:num)', 'Pembelian::detail/$1');
        $routes->get('pembelian/delete/(:num)', 'Pembelian::delete/$1');
        
        // Stok Management
        $routes->get('stok', 'Stok::index');
        $routes->get('stok/detail/(:num)', 'Stok::detail/$1');
        $routes->get('stok/opname/(:num)', 'Stok::opname/$1');
        $routes->post('stok/update-opname/(:num)', 'Stok::updateOpname/$1');
        $routes->get('stok/history', 'Stok::history');
    });

    // ========== KASIR ROUTES (Kasir & Admin bisa akses) ==========
    $routes->group('kasir', ['namespace' => 'App\Controllers\Kasir'], function ($routes) {
        
        // Dashboard
        $routes->get('dashboard', 'Dashboard::index');
        
        // Penjualan
        $routes->get('penjualan', 'Penjualan::index');
        $routes->get('penjualan/create', 'Penjualan::create');
        $routes->post('penjualan/store', 'Penjualan::store');
        $routes->get('penjualan/struk/(:num)', 'Penjualan::struk/$1');
        $routes->get('penjualan/batal/(:num)', 'Penjualan::batal/$1');
        $routes->get('penjualan/search-produk', 'Penjualan::searchProduk');
        $routes->get('penjualan/get-struk-data/(:num)', 'Penjualan::getStrukData/$1');
        
        // Kasir Dashboard API
        $routes->group('dashboard', function ($routes) {
            $routes->get('getWeeklySalesChart', 'Dashboard::getWeeklySalesChart');
            $routes->get('getPaymentMethodChart', 'Dashboard::getPaymentMethodChart');
        });
    });
});