<div id="sidebar">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="<?= base_url('dashboard') ?>">Omah Nini</a>
                </div>
                <div class="sidebar-toggler x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Utama</li>

                <li class="sidebar-item <?= (uri_string() == 'dashboard') ? 'active' : '' ?>">
                    <a href="<?= base_url('dashboard') ?>" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <?php $role = session()->get('role'); ?>

                <?php if ($role === 'admin') : ?>
                    <li class="sidebar-title">Master Data</li>
                    <li class="sidebar-item has-sub <?= (in_array(uri_string(), ['kategori', 'supplier', 'users'])) ? 'active' : '' ?>">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-stack"></i>
                            <span>Master Data</span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item <?= (uri_string() == 'kategori') ? 'active' : '' ?>">
                                <a href="<?= base_url('kategori') ?>" class="submenu-link">Kategori</a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'produk') ? 'active' : '' ?>">
                                <a href="<?= base_url('produk') ?>" class="submenu-link">Produk</a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'supplier') ? 'active' : '' ?>">
                                <a href="<?= base_url('supplier') ?>" class="submenu-link">Supplier</a>
                            </li>
                            <li class="submenu-item <?= (uri_string() == 'users') ? 'active' : '' ?>">
                                <a href="<?= base_url('users') ?>" class="submenu-link">Kelola User</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (in_array($role, ['kasir', 'admin'])) : ?>
                    <li class="sidebar-title">Penjualan</li>
                    <li class="sidebar-item <?= (uri_string() == 'kasir') ? 'active' : '' ?>">
                        <a href="<?= base_url('kasir') ?>" class='sidebar-link'>
                            <i class="bi bi-cart-fill"></i>
                            <span>Kasir (POS)</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (in_array($role, ['gudang', 'admin'])) : ?>
                    <li class="sidebar-title">Logistik</li>
                    <li class="sidebar-item <?= (uri_string() == 'pembelian') ? 'active' : '' ?>">
                        <a href="<?= base_url('pembelian') ?>" class='sidebar-link'>
                            <i class="bi bi-truck"></i>
                            <span>Pembelian Barang</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'admin') : ?>
                    <li class="sidebar-title">Laporan & Audit</li>
                    <li class="sidebar-item <?= (uri_string() == 'laporan-penjualan') ? 'active' : '' ?>">
                        <a href="<?= base_url('laporan-penjualan') ?>" class='sidebar-link'>
                            <i class="bi bi-file-earmark-bar-graph-fill"></i>
                            <span>Laporan Penjualan</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?= (uri_string() == 'laporan-pembelian') ? 'active' : '' ?>">
                        <a href="<?= base_url('laporan-pembelian') ?>" class='sidebar-link'>
                            <i class="bi bi-file-earmark-arrow-down-fill"></i>
                            <span>Laporan Pembelian</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?= (uri_string() == 'stok-log') ? 'active' : '' ?>">
                        <a href="<?= base_url('stok-log') ?>" class='sidebar-link'>
                            <i class="bi bi-journal-text"></i>
                            <span>Audit Stok</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="sidebar-title">Sesi</li>
                <li class="sidebar-item">
                    <a href="<?= base_url('logout') ?>" class='sidebar-link text-danger'>
                        <i class="bi bi-door-open-fill text-danger"></i>
                        <span>Keluar</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>