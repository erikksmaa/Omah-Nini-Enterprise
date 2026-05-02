<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
        <div>
            <span class="badge bg-primary"><?= ucfirst($role) ?></span>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row">
        <!-- Total Produk -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Produk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_produk) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Stok -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Stok</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_stok) ?></div>
                            <small>Potong</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-boxes fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barang Masuk Bulan Ini -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Barang Masuk (Bln Ini)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $pembelian_bulan_ini ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-truck fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barang Keluar Bulan Ini -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Barang Keluar (Bln Ini)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $penjualan_bulan_ini ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-cart-check fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Hari Ini dan Ringkasan -->
    <div class="row">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Hari Ini</h6>
                </div>
                <div class="card-body text-center">
                    <h4>Transaksi Hari Ini</h4>
                    <h1 class="display-3 text-primary"><?= $transaksi_hari_ini ?></h1>
                    <p class="text-muted">Jumlah transaksi penjualan</p>
                    <a href="<?= base_url('karyawan/penjualan') ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-receipt"></i> Lihat Riwayat Penjualan
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="<?= base_url('karyawan/pembelian/create') ?>" class="btn btn-success btn-lg">
                            <i class="bi bi-box-seam"></i> Barang Masuk (Pembelian)
                        </a>
                        <a href="<?= base_url('karyawan/penjualan/create') ?>" class="btn btn-info btn-lg">
                            <i class="bi bi-cart-plus"></i> Barang Keluar (POS)
                        </a>
                        <a href="<?= base_url('karyawan/stok') ?>" class="btn btn-warning btn-lg">
                            <i class="bi bi-box-seam"></i> Manajemen Stok
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Peringatan Stok Menipis -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="bi bi-exclamation-triangle"></i> Peringatan Stok Menipis
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($stok_menipis)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Produk</th>
                                        <th>Stok Saat Ini</th>
                                        <th>Stok Minimal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stok_menipis as $item): ?>
                                        <tr>
                                            <td><?= esc($item['sku']) ?></td>
                                            <td><?= esc($item['nama_produk']) ?></td>
                                            <td><span class="badge bg-warning text-dark"><?= number_format($item['stok']) ?></span></td>
                                            <td><?= number_format($item['min_stok']) ?></td>
                                            <td>
                                                <?php if ($item['stok'] == 0): ?>
                                                    <span class="badge bg-danger">Habis</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Menipis</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('karyawan/stok/detail/' . $item['id']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> Detail Stok
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> Semua stok dalam kondisi aman.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>