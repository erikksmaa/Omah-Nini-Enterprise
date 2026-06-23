<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="<?= base_url('dashboard') ?>">Syarifa Batik Inventory</a>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu">
                <?php
                $role = session()->get('role');
                $uri = uri_string();

                // Active state: Master Data (sama untuk admin & karyawan, route identik)
                $masterPages = ['admin/supplier', 'admin/motif', 'admin/warna', 'admin/produk', 'admin/pelanggan'];
                $masterPagesAdminOnly = ['admin/user'];
                $isMasterActive = in_array($uri, array_merge($masterPages, $masterPagesAdminOnly));
                $isMasterKaryawanActive = in_array($uri, $masterPages);

                // Active state: Laporan
                $isLaporanActive = str_starts_with($uri, 'pemilik/laporan');
                ?>

                <?php if ($role === 'admin' || $role === 'karyawan'): ?>

                    <?php $dashboard = ($role === 'admin') ? 'admin/dashboard' : 'karyawan/dashboard'; ?>

                    <!-- ===== UTAMA ===== -->
                    <li class="sidebar-title">Utama</li>
                    <li class="sidebar-item <?= ($uri == $dashboard) ? 'active' : '' ?>">
                        <a href="<?= base_url($dashboard) ?>" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- ===== MASTER DATA ===== -->
                    <li class="sidebar-title">Master</li>
                    <li
                        class="sidebar-item has-sub <?= ($role === 'admin' ? $isMasterActive : $isMasterKaryawanActive) ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-database-fill"></i>
                            <span>Master Data</span>
                        </a>
                        <ul
                            class="submenu <?= ($role === 'admin' ? $isMasterActive : $isMasterKaryawanActive) ? 'active' : '' ?>">
                            <li class="submenu-item <?= ($uri == 'admin/supplier') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/supplier') ?>" class="submenu-link">
                                    <i class="bi bi-building"></i> Merk / Brand
                                </a>
                            </li>
                            <li class="submenu-item <?= ($uri == 'admin/motif') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/motif') ?>" class="submenu-link">
                                    <i class="bi bi-brush"></i> Motif
                                </a>
                            </li>
                            <li class="submenu-item <?= ($uri == 'admin/warna') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/warna') ?>" class="submenu-link">
                                    <i class="bi bi-palette"></i> Warna
                                </a>
                            </li>
                            <li class="submenu-item <?= ($uri == 'admin/produk') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/produk') ?>" class="submenu-link">
                                    <i class="bi bi-box"></i> Produk
                                </a>
                            </li>
                            <li class="submenu-item <?= ($uri == 'admin/pelanggan') ? 'active' : '' ?>">
                                <a href="<?= base_url('admin/pelanggan') ?>" class="submenu-link">
                                    <i class="bi bi-people"></i> Pelanggan
                                </a>
                            </li>
                            <?php if ($role === 'admin'): ?>
                                <li class="submenu-item <?= ($uri == 'admin/user') ? 'active' : '' ?>">
                                    <a href="<?= base_url('admin/user') ?>" class="submenu-link">
                                        <i class="bi bi-person-badge"></i> Manajemen User
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <!-- ===== MANAJEMEN STOK (standalone) ===== -->
                    <li class="sidebar-item <?= ($uri == 'karyawan/stok') ? 'active' : '' ?>">
                        <a href="<?= base_url('karyawan/stok') ?>" class="sidebar-link">
                            <i class="bi bi-boxes"></i>
                            <span>Manajemen Stok</span>
                        </a>
                    </li>

                    <!-- ===== TRANSAKSI ===== -->
                    <li class="sidebar-title">Transaksi</li>

                    <li
                        class="sidebar-item <?= in_array($uri, ['karyawan/pembelian', 'karyawan/pembelian/create']) ? 'active' : '' ?>">
                        <a href="<?= base_url('karyawan/pembelian') ?>" class="sidebar-link">
                            <i class="bi bi-box-seam"></i>
                            <span>Barang Masuk</span>
                        </a>
                    </li>

                    <li
                        class="sidebar-item <?= in_array($uri, ['karyawan/penjualan', 'karyawan/penjualan/create']) ? 'active' : '' ?>">
                        <a href="<?= base_url('karyawan/penjualan') ?>" class="sidebar-link">
                            <i class="bi bi-cart-plus"></i>
                            <span>Barang Keluar (POS)</span>
                        </a>
                    </li>

                    <!-- ===== LAPORAN — hanya admin ===== -->
                    <?php if ($role === 'admin'): ?>
                        <li class="sidebar-title">Laporan</li>
                        <li class="sidebar-item has-sub <?= $isLaporanActive ? 'active' : '' ?>">
                            <a href="#" class="sidebar-link">
                                <i class="bi bi-file-earmark-bar-graph-fill"></i>
                                <span>Laporan</span>
                            </a>
                            <ul class="submenu <?= $isLaporanActive ? 'active' : '' ?>">
                                <li class="submenu-item <?= ($uri == 'pemilik/laporan/stok') ? 'active' : '' ?>">
                                    <a href="<?= base_url('pemilik/laporan/stok') ?>" class="submenu-link">
                                        <i class="bi bi-box-seam"></i> Laporan Stok
                                    </a>
                                </li>
                                <li class="submenu-item <?= ($uri == 'pemilik/laporan/log-stok') ? 'active' : '' ?>">
                                    <a href="<?= base_url('pemilik/laporan/log-stok') ?>" class="submenu-link">
                                        <i class="bi bi-clock-history"></i> Log Stok
                                    </a>
                                </li>
                                <li class="submenu-item <?= ($uri == 'pemilik/laporan/barang-masuk') ? 'active' : '' ?>">
                                    <a href="<?= base_url('pemilik/laporan/barang-masuk') ?>" class="submenu-link">
                                        <i class="bi bi-truck"></i> Laporan Barang Masuk
                                    </a>
                                </li>
                                <li class="submenu-item <?= ($uri == 'pemilik/laporan/barang-keluar') ? 'active' : '' ?>">
                                    <a href="<?= base_url('pemilik/laporan/barang-keluar') ?>" class="submenu-link">
                                        <i class="bi bi-receipt"></i> Laporan Barang Keluar
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>


                <?php elseif ($role === 'pemilik'): ?>

                    <!-- ===== MENU PEMILIK ===== -->
                    <li class="sidebar-title">Utama</li>
                    <li class="sidebar-item <?= ($uri == 'pemilik/dashboard') ? 'active' : '' ?>">
                        <a href="<?= base_url('pemilik/dashboard') ?>" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-title">Laporan</li>
                    <li class="sidebar-item has-sub <?= $isLaporanActive ? 'active' : '' ?>">
                        <a href="#" class="sidebar-link">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i>
                            <span>Laporan</span>
                        </a>
                        <ul class="submenu <?= $isLaporanActive ? 'active' : '' ?>">
                            <li class="submenu-item <?= ($uri == 'pemilik/laporan/stok') ? 'active' : '' ?>">
                                <a href="<?= base_url('pemilik/laporan/stok') ?>" class="submenu-link">
                                    <i class="bi bi-box-seam"></i> Laporan Stok
                                </a>
                            </li>
                            <li class="submenu-item <?= ($uri == 'pemilik/laporan/log-stok') ? 'active' : '' ?>">
                                <a href="<?= base_url('pemilik/laporan/log-stok') ?>" class="submenu-link">
                                    <i class="bi bi-clock-history"></i> Log Stok
                                </a>
                            </li>
                            <li class="submenu-item <?= ($uri == 'pemilik/laporan/barang-masuk') ? 'active' : '' ?>">
                                <a href="<?= base_url('pemilik/laporan/barang-masuk') ?>" class="submenu-link">
                                    <i class="bi bi-truck"></i> Barang Masuk
                                </a>
                            </li>
                            <li class="submenu-item <?= ($uri == 'pemilik/laporan/barang-keluar') ? 'active' : '' ?>">
                                <a href="<?= base_url('pemilik/laporan/barang-keluar') ?>" class="submenu-link">
                                    <i class="bi bi-receipt"></i> Barang Keluar
                                </a>
                            </li>
                        </ul>
                    </li>

                <?php endif; ?>

                <!-- ===== SESI (semua role) ===== -->
                <li class="sidebar-title">Sesi</li>
                <li class="sidebar-item">
                    <a href="#" id="btnLogoutSidebar" class="sidebar-link">
                        <i class="bi bi-door-open-fill"></i>
                        <span>Keluar</span>
                    </a>
                </li>
                <?php if ($role === 'admin'): ?>
                    <li class="sidebar-title">Sistem</li>
                    <li class="sidebar-item <?= (uri_string() == 'admin/backup') ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/backup') ?>" class="sidebar-link">
                            <i class="bi bi-database"></i>
                            <span>Backup Database</span>
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</div>

<style>
    .sidebar-wrapper {
        background: var(--sidebar-bg) !important;
        border-right: 1px solid var(--card-border) !important;
    }

    .sidebar-menu {
        padding-bottom: 100px;
    }

    /* ... style lainnya tetap ... */
</style>
