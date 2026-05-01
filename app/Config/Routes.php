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

    // app/Config/Routes.php

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
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'role:admin'], function ($routes) {
    
    // Dashboard Admin
    $routes->get('dashboard', 'Dashboard::index');
    
    // Master Data
    $routes->get('supplier', 'Supplier::index');
    $routes->post('supplier/store', 'Supplier::store');
    $routes->post('supplier/update/(:num)', 'Supplier::update/$1');
    $routes->get('supplier/delete/(:num)', 'Supplier::delete/$1');
    
    $routes->get('motif', 'Motif::index');
    $routes->post('motif/store', 'Motif::store');
    $routes->post('motif/update/(:num)', 'Motif::update/$1');
    $routes->get('motif/delete/(:num)', 'Motif::delete/$1');
    
    $routes->get('warna', 'Warna::index');
    $routes->post('warna/store', 'Warna::store');
    $routes->post('warna/update/(:num)', 'Warna::update/$1');
    $routes->get('warna/delete/(:num)', 'Warna::delete/$1');
    
    $routes->get('produk', 'Produk::index');
    $routes->get('produk/create', 'Produk::create');
    $routes->post('produk/store', 'Produk::store');
    $routes->get('produk/edit/(:num)', 'Produk::edit/$1');
    $routes->post('produk/update/(:num)', 'Produk::update/$1');
    $routes->get('produk/delete/(:num)', 'Produk::delete/$1');
    $routes->post('produk/generateSku', 'Produk::generateSku');
    $routes->get('produk/getMotifBySupplierAjax', 'Produk::getMotifBySupplierAjax');
    
    $routes->get('pelanggan', 'Pelanggan::index');
    $routes->post('pelanggan/store', 'Pelanggan::store');
    $routes->post('pelanggan/update/(:num)', 'Pelanggan::update/$1');
    $routes->get('pelanggan/delete/(:num)', 'Pelanggan::delete/$1');
    $routes->get('pelanggan/getData/(:num)', 'Pelanggan::getData/$1');
    $routes->get('pelanggan/search', 'Pelanggan::search');
    
    // USER MANAGEMENT (HANYA ADMIN)
    $routes->get('user', 'User::index');
    $routes->post('user/store', 'User::store');
    $routes->post('user/update/(:num)', 'User::update/$1');
    $routes->get('user/delete/(:num)', 'User::delete/$1');
    $routes->get('user/getData/(:num)', 'User::getData/$1');
    
    // Laporan
    $routes->group('laporan', function ($routes) {
        $routes->get('stok', 'Laporan::stok');
        $routes->get('barang-masuk', 'Laporan::barangMasuk');
        $routes->get('barang-keluar', 'Laporan::barangKeluar');
        $routes->get('log-stok', 'Laporan::logStok');
    });
});

        // ========== KARYAWAN ROUTES (Karyawan & Admin bisa akses) ==========
        $routes->group('karyawan', ['namespace' => 'App\Controllers\Karyawan', 'filter' => 'role:karyawan,admin'], function ($routes) {

            // Dashboard
            $routes->get('dashboard', 'Dashboard::index');

            // Barang Masuk (Pembelian)
            $routes->get('pembelian', 'Pembelian::index');
            $routes->get('pembelian/create', 'Pembelian::create');
            $routes->post('pembelian/store', 'Pembelian::store');
            $routes->get('pembelian/detail/(:num)', 'Pembelian::detail/$1');

            // Barang Keluar (Penjualan)
            $routes->get('penjualan', 'Penjualan::index');
            $routes->get('penjualan/create', 'Penjualan::create');
            $routes->post('penjualan/store', 'Penjualan::store');
            $routes->get('penjualan/struk/(:num)', 'Penjualan::struk/$1');

            // Manajemen Stok
            $routes->get('stok', 'Stok::index');
            $routes->get('stok/detail/(:num)', 'Stok::detail/$1');
            $routes->get('stok/opname/(:num)', 'Stok::opname/$1');
            $routes->post('stok/update-opname/(:num)', 'Stok::updateOpname/$1');
            $routes->get('stok/history', 'Stok::history');
        });
    });

    // ========== API ROUTES ==========
    $routes->group('api', ['filter' => 'auth'], function ($routes) {
        $routes->post('search/produk', 'Api\Search::produk');
        $routes->get('motif/by-supplier/(:num)', 'Api\Motif::getBySupplier/$1');
        $routes->get('warna/all', 'Api\Warna::all');
        $routes->get('produk/by-motif/(:num)', 'Api\Produk::getByMotif/$1');
    });
});