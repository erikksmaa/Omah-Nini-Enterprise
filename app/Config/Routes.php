<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Route tanpa auth
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

// Route dengan auth
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // Dashboard berdasarkan role
    $routes->get('admin/dashboard', 'Admin\Dashboard::index');
    $routes->get('gudang/dashboard', 'Gudang\Dashboard::index');
    $routes->get('kasir/dashboard', 'Kasir\Dashboard::index');

    // Redirect ke dashboard sesuai role
    $routes->get('/dashboard', function () {
        $role = session()->get('role');
        return redirect()->to("/{$role}/dashboard");
    });

    // ========== GUDANG ROUTES ==========
    $routes->group('gudang', ['namespace' => 'App\Controllers\Gudang'], function ($routes) {
        // Dashboard
        $routes->get('dashboard', 'Dashboard::index');

        // Pembelian
        $routes->get('pembelian', 'Pembelian::index');
        $routes->get('pembelian/create', 'Pembelian::create');
        $routes->post('pembelian/store', 'Pembelian::store');
        $routes->get('pembelian/detail/(:num)', 'Pembelian::detail/$1');
        $routes->get('pembelian/delete/(:num)', 'Pembelian::delete/$1');

        // Stok
        $routes->get('stok', 'Stok::index');
        $routes->get('stok/detail/(:num)', 'Stok::detail/$1');
        $routes->get('stok/opname/(:num)', 'Stok::opname/$1');
        $routes->post('stok/update-opname/(:num)', 'Stok::updateOpname/$1');
        $routes->get('stok/history', 'Stok::history');


    });

    // ========== ADMIN ROUTES ==========
    $routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
        $routes->get('dashboard', 'Dashboard::index');
        $routes->get('kategori', 'Kategori::index');
        $routes->post('kategori/store', 'Kategori::store');
        $routes->post('kategori/update/(:num)', 'Kategori::update/$1');
        $routes->get('kategori/delete/(:num)', 'Kategori::delete/$1');

        $routes->get('produk', 'Produk::index');
        $routes->post('produk/store', 'Produk::store');
        $routes->post('produk/update/(:num)', 'Produk::update/$1');
        $routes->get('produk/delete/(:num)', 'Produk::delete/$1');

        $routes->get('supplier', 'Supplier::index');
        $routes->post('supplier/store', 'Supplier::store');
        $routes->post('supplier/update/(:num)', 'Supplier::update/$1');
        $routes->get('supplier/delete/(:num)', 'Supplier::delete/$1');

        $routes->get('user', 'User::index');
        $routes->post('user/store', 'User::store');
        $routes->post('user/update/(:num)', 'User::update/$1');
        $routes->get('user/delete/(:num)', 'User::delete/$1');

        // Laporan Routes
        $routes->get('laporan', 'Laporan::index');
        $routes->get('laporan/keuangan', 'Laporan::keuangan');
        $routes->get('laporan/penjualan', 'Laporan::penjualan');
        $routes->get('laporan/pembelian', 'Laporan::pembelian');
        $routes->get('laporan/laba-rugi', 'Laporan::labaRugi');
        $routes->get('laporan/log-stok', 'Laporan::logStok');
        $routes->get('laporan/produk', 'Laporan::produk');
        $routes->get('laporan/export-penjualan', 'Laporan::exportPenjualan');

        // Pembelian
        $routes->get('pembelian', 'Pembelian::index');
        $routes->get('pembelian/create', 'Pembelian::create');
        $routes->post('pembelian/store', 'Pembelian::store');
        $routes->get('pembelian/detail/(:num)', 'Pembelian::detail/$1');

        // Stok Management
        $routes->get('stok', 'Stok::index');
        $routes->get('stok/detail/(:num)', 'Stok::detail/$1');
        $routes->get('stok/opname/(:num)', 'Stok::opname/$1');
        $routes->post('stok/update-opname/(:num)', 'Stok::updateOpname/$1');
        $routes->get('stok/history', 'Stok::history');

        // Retur Penjualan
        $routes->get('retur', 'Retur::index');
        $routes->get('retur/create', 'Retur::create');
        $routes->post('retur/store', 'Retur::store');
        $routes->get('retur/test-insert', 'Retur::testInsert');
        $routes->get('retur/detail/(:num)', 'Retur::detail/$1');
        $routes->get('retur/delete/(:num)', 'Retur::delete/$1');
        $routes->get('retur/getDetailTransaksi/(:num)', 'Retur::getDetailTransaksi/$1');
            $routes->get('retur/laporan', 'Retur::laporan');
    $routes->get('retur/export-excel', 'Retur::exportExcel');
    });

    // Kasir
    $routes->group('kasir', ['namespace' => 'App\Controllers\Kasir', 'filter' => 'auth'], function ($routes) {
        $routes->get('dashboard', 'Dashboard::index');
        $routes->get('penjualan', 'Penjualan::index');
        $routes->get('penjualan/create', 'Penjualan::create');
        $routes->post('penjualan/store', 'Penjualan::store');
        $routes->get('penjualan/struk/(:num)', 'Penjualan::struk/$1');
        $routes->get('penjualan/batal/(:num)', 'Penjualan::batal/$1');
        $routes->get('penjualan/search-produk', 'Penjualan::searchProduk');
        $routes->get('penjualan/get-struk-data/(:num)', 'Penjualan::getStrukData/$1'); // Tambah ini
    });

    $routes->group('admin/dashboard', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth'], function ($routes) {
        $routes->get('getWeeklySalesChart', 'Dashboard::getWeeklySalesChart');
        $routes->get('getMonthlySalesChart', 'Dashboard::getMonthlySalesChart');
        $routes->get('getProfitLossChart', 'Dashboard::getProfitLossChart');
        $routes->get('getLowStockData', 'Dashboard::getLowStockData');
        $routes->get('getTopProductsChart', 'Dashboard::getTopProductsChart');
        $routes->get('getPaymentMethodChart', 'Dashboard::getPaymentMethodChart');
        $routes->get('getDashboardData', 'Dashboard::getDashboardData');
    });

    // Kasir Dashboard API Routes
    $routes->group('kasir/dashboard', ['namespace' => 'App\Controllers\Kasir', 'filter' => 'auth'], function ($routes) {
        $routes->get('getWeeklySalesChart', 'Dashboard::getWeeklySalesChart');
        $routes->get('getPaymentMethodChart', 'Dashboard::getPaymentMethodChart');
    });

});