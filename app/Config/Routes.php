<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Dashboard::index');
$routes->get('/dashboard', 'Dashboard::index');

$routes->get('/', 'Auth::index');
$routes->get('/login', 'Auth::index');
$routes->post('/auth/process', 'Auth::process');
$routes->get('/logout', 'Auth::logout');

// Lindungi dashboard menggunakan filter yang sudah kita buat
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'role:pemilik,admin,gudang,kasir']);

// Route untuk Admin
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function($routes) {
    // User routes
    $routes->get('user', 'User::index');
    $routes->post('user/store', 'User::store');
    $routes->post('user/update/(:num)', 'User::update/$1');
    $routes->get('user/delete/(:num)', 'User::delete/$1');
    
    // Produk routes
    $routes->get('produk', 'Produk::index');
    $routes->post('produk/store', 'Produk::store');
    $routes->post('produk/update/(:num)', 'Produk::update/$1');
    $routes->get('produk/delete/(:num)', 'Produk::delete/$1');
    
    // Kategori routes
    $routes->get('kategori', 'Kategori::index');
    $routes->post('kategori/store', 'Kategori::store');
    $routes->post('kategori/update/(:num)', 'Kategori::update/$1');
    $routes->get('kategori/delete/(:num)', 'Kategori::delete/$1');
    
    // Supplier routes
    $routes->get('supplier', 'Supplier::index');
    $routes->post('supplier/store', 'Supplier::store');
    $routes->post('supplier/update/(:num)', 'Supplier::update/$1');
    $routes->get('supplier/delete/(:num)', 'Supplier::delete/$1');
});