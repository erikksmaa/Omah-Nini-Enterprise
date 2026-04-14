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