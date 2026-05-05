<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="<?= base_url('dashboard') ?>">Batik Inventory</a>
                </div>
            </div>
            <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                <!-- dark mode toggle (tetap sama) -->
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 21 21">
                    <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                            opacity=".3"></path>
                    </g>
                </svg>
                <div class="form-check form-switch fs-6">
                    <input class="form-check-input me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                    <label class="form-check-label"></label>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                    </path>
                </svg>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu">
                <?php $role = session()->get('role'); ?>

                <!-- ========== ADMIN MENU ========== -->
                <?php if ($role === 'admin'): ?>

                    <li class="sidebar-title">Utama</li>
                    <li class="sidebar-item <?= (uri_string() == 'admin/dashboard') ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/dashboard') ?>" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li
                        class="sidebar-item has-sub <?= in_array(uri_string(), ['admin/supplier', 'admin/motif', 'admin/warna', 'admin/produk', 'admin/pelanggan', 'admin/user']) ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-database-fill"></i>
                            <span>Master Data</span>
                        </a>
                        <ul
                            class="submenu <?= in_array(uri_string(), ['admin/supplier', 'admin/motif', 'admin/warna', 'admin/produk', 'admin/pelanggan', 'admin/user']) ? 'active' : '' ?>">
                            <li class="submenu-item <?= (uri_string() == 'admin/supplier') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/supplier') ?>" class="submenu-link">
                                    <i class="bi bi-building"></i> Merk / Brand
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'admin/motif') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/motif') ?>" class="submenu-link">
                                    <i class="bi bi-brush"></i> Motif
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'admin/warna') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/warna') ?>" class="submenu-link">
                                    <i class="bi bi-palette"></i> Warna
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'admin/produk') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/produk') ?>" class="submenu-link">
                                    <i class="bi bi-box"></i> Produk
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'admin/pelanggan') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/pelanggan') ?>" class="submenu-link">
                                    <i class="bi bi-people"></i> Pelanggan
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'admin/user') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/user') ?>" class="submenu-link">
                                    <i class="bi bi-person-badge"></i> Manajemen User
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li
                        class="sidebar-item has-sub <?= in_array(uri_string(), ['karyawan/pembelian/create', 'karyawan/penjualan/create']) ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-arrow-left-right"></i>
                            <span>Transaksi</span>
                        </a>
                        <ul
                            class="submenu <?= in_array(uri_string(), ['karyawan/pembelian/create', 'karyawan/penjualan/create']) ? 'active' : '' ?>">
                            <li class="submenu-item <?= (uri_string() == 'karyawan/pembelian/create') ? 'active' : '' ?>">
                                <a href="<?= base_url('karyawan/pembelian/create') ?>" class="submenu-link">
                                    <i class="bi bi-box-seam"></i> Barang Masuk
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'karyawan/penjualan/create') ? 'active' : '' ?>">
                                <a href="<?= base_url('karyawan/penjualan/create') ?>" class="submenu-link">
                                    <i class="bi bi-cart-plus"></i> Barang Keluar (POS)
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li
                        class="sidebar-item has-sub <?= in_array(uri_string(), ['karyawan/stok', 'karyawan/pembelian', 'karyawan/penjualan']) ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-box-seam"></i>
                            <span>Stok &amp; Riwayat</span>
                        </a>
                        <ul
                            class="submenu <?= in_array(uri_string(), ['karyawan/stok', 'karyawan/pembelian', 'karyawan/penjualan']) ? 'active' : '' ?>">
                            <li class="submenu-item <?= (uri_string() == 'karyawan/stok') ? 'active' : '' ?>">
                                <a href="<?= base_url('karyawan/stok') ?>" class="submenu-link">
                                    <i class="bi bi-box-seam"></i> Manajemen Stok
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'karyawan/pembelian') ? 'active' : '' ?>">
                                <a href="<?= base_url('karyawan/pembelian') ?>" class="submenu-link">
                                    <i class="bi bi-truck"></i> Riwayat Barang Masuk
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'karyawan/penjualan') ? 'active' : '' ?>">
                                <a href="<?= base_url('karyawan/penjualan') ?>" class="submenu-link">
                                    <i class="bi bi-receipt"></i> Riwayat Penjualan
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li
                        class="sidebar-item has-sub <?= in_array(uri_string(), ['admin/laporan/stok', 'admin/laporan/barang-masuk', 'admin/laporan/barang-keluar', 'admin/laporan/log-stok']) ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i>
                            <span>Laporan</span>
                        </a>
                        <ul
                            class="submenu <?= in_array(uri_string(), ['pemilik/laporan/stok', 'pemilik/laporan/barang-masuk', 'pemilik/laporan/barang-keluar', 'pemilik/laporan/log-stok']) ? 'active' : '' ?>">
                            <li class="submenu-item <?= (uri_string() == 'pemilik/laporan/stok') ? 'active' : '' ?>">
                                <a href="<?= base_url('pemilik/laporan/stok') ?>" class="submenu-link">
                                    <i class="bi bi-box-seam"></i> Laporan Stok
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'pemilik/laporan/barang-masuk') ? 'active' : '' ?>">
                                <a href="<?= base_url('pemilik/laporan/barang-masuk') ?>" class="submenu-link">
                                    <i class="bi bi-truck"></i> Laporan Barang Masuk
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'pemilik/laporan/barang-keluar') ? 'active' : '' ?>">
                                <a href="<?= base_url('pemilik/laporan/barang-keluar') ?>" class="submenu-link">
                                    <i class="bi bi-receipt"></i> Laporan Barang Keluar
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'pemilik/laporan/log-stok') ? 'active' : '' ?>">
                                <a href="<?= base_url('pemilik/laporan/log-stok') ?>" class="submenu-link">
                                    <i class="bi bi-clock-history"></i> Log Stok
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- ========== PEMILIK MENU ========== -->
                <?php elseif ($role === 'pemilik'): ?>
                    <li class="sidebar-item <?= (uri_string() == 'pemilik/dashboard') ? 'active' : '' ?>">
                        <a href="<?= base_url('pemilik/dashboard') ?>" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li
                        class="sidebar-item has-sub <?= str_starts_with(uri_string(), 'pemilik/laporan') ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i>
                            <span>Laporan</span>
                        </a>
                        <ul class="submenu <?= str_starts_with(uri_string(), 'pemilik/laporan') ? 'active' : '' ?>">
                            <li class="submenu-item <?= (uri_string() == 'pemilik/laporan/stok') ? 'active' : '' ?>">
                                <a href="<?= base_url('pemilik/laporan/stok') ?>" class="submenu-link">
                                    <i class="bi bi-box-seam"></i> Laporan Stok
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'pemilik/laporan/log-stok') ? 'active' : '' ?>">
                                <a href="<?= base_url('pemilik/laporan/log-stok') ?>" class="submenu-link">
                                    <i class="bi bi-clock-history"></i> Log Stok
                                </a>
                            </li>
                            <li
                                class="submenu-item <?= (uri_string() == 'pemilik/laporan/barang-masuk') ? 'active' : '' ?>">
                                <a href="<?= base_url('pemilik/laporan/barang-masuk') ?>" class="submenu-link">
                                    <i class="bi bi-truck"></i> Barang Masuk
                                </a>
                            </li>
                            <li
                                class="submenu-item <?= (uri_string() == 'pemilik/laporan/barang-keluar') ? 'active' : '' ?>">
                                <a href="<?= base_url('pemilik/laporan/barang-keluar') ?>" class="submenu-link">
                                    <i class="bi bi-receipt"></i> Barang Keluar
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- ========== KARYAWAN MENU ========== -->
                <?php elseif ($role === 'karyawan'): ?>

                    <li class="sidebar-title">Utama</li>
                    <li class="sidebar-item <?= (uri_string() == 'karyawan/dashboard') ? 'active' : '' ?>">
                        <a href="<?= base_url('karyawan/dashboard') ?>" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li
                        class="sidebar-item has-sub <?= in_array(uri_string(), ['karyawan/pembelian/create', 'karyawan/penjualan/create']) ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-arrow-left-right"></i>
                            <span>Transaksi</span>
                        </a>
                        <ul
                            class="submenu <?= in_array(uri_string(), ['karyawan/pembelian/create', 'karyawan/penjualan/create']) ? 'active' : '' ?>">
                            <li class="submenu-item <?= (uri_string() == 'karyawan/pembelian/create') ? 'active' : '' ?>">
                                <a href="<?= base_url('karyawan/pembelian/create') ?>" class="submenu-link">
                                    <i class="bi bi-box-seam"></i> Barang Masuk
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'karyawan/penjualan/create') ? 'active' : '' ?>">
                                <a href="<?= base_url('karyawan/penjualan/create') ?>" class="submenu-link">
                                    <i class="bi bi-cart-plus"></i> Barang Keluar (POS)
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li
                        class="sidebar-item has-sub <?= in_array(uri_string(), ['karyawan/stok', 'karyawan/pembelian', 'karyawan/penjualan']) ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-box-seam"></i>
                            <span>Stok &amp; Riwayat</span>
                        </a>
                        <ul
                            class="submenu <?= in_array(uri_string(), ['karyawan/stok', 'karyawan/pembelian', 'karyawan/penjualan']) ? 'active' : '' ?>">
                            <li class="submenu-item <?= (uri_string() == 'karyawan/stok') ? 'active' : '' ?>">
                                <a href="<?= base_url('karyawan/stok') ?>" class="submenu-link">
                                    <i class="bi bi-box-seam"></i> Manajemen Stok
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'karyawan/pembelian') ? 'active' : '' ?>">
                                <a href="<?= base_url('karyawan/pembelian') ?>" class="submenu-link">
                                    <i class="bi bi-truck"></i> Riwayat Barang Masuk
                                </a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'karyawan/penjualan') ? 'active' : '' ?>">
                                <a href="<?= base_url('karyawan/penjualan') ?>" class="submenu-link">
                                    <i class="bi bi-receipt"></i> Riwayat Penjualan
                                </a>
                            </li>
                        </ul>
                    </li>

                <?php endif; ?>

                <!-- Sesi (sama untuk semua role) -->
                <li class="sidebar-title">Sesi</li>
                <li class="sidebar-item">
                    <a href="#" id="btnLogoutSidebar" class="sidebar-link">
                        <i class="bi bi-door-open-fill"></i>
                        <span>Keluar</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>

<!-- Style dan dark mode script (tetap sama seperti yang Anda punya) -->
<style>
    /* Sidebar Styles (tetap sama) */
    .sidebar-wrapper {
        background: var(--sidebar-bg) !important;
        border-right: 1px solid var(--card-border) !important;
    }

    /* ... style lainnya tetap ... */
</style>

<script>
    // Dark mode toggle (tetap sama)
    const toggleDark = document.getElementById('toggle-dark');
    if (toggleDark) {
        toggleDark.addEventListener('change', function () {
            if (this.checked) {
                document.body.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.body.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        });

        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark');
            toggleDark.checked = true;
        }
    }
</script>