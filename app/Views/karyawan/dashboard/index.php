<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <!-- Welcome Banner -->
    <div class="row">
        <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #435ebe 0%, #1a2fa0 100%); border: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-white">Selamat Datang, <?= $username ?>!</h4>
                            <p class="mb-0 text-white">📅 <?= date('l, d F Y') ?></p>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-1 text-white">Role: <span class="badge bg-light text-dark"><?= $role ?></span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Produk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_produk) ?>
                            </div>
                        </div>
                        <div class="col-auto"><i class="bi bi-box-seam fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Stok</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_stok) ?></div>
                        </div>
                        <div class="col-auto"><i class="bi bi-calculator fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Transaksi Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($transaksi_hari_ini) ?>x</div>
                        </div>
                        <div class="col-auto"><i class="bi bi-receipt fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Bulan Ini -->
    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-calendar"></i> Aktivitas Bulan Ini
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="card bg-success text-white">
                                <div class="card-body py-3">
                                    <h5 class="text-white">Barang Masuk</h5>
                                    <h3><?= number_format($pembelian_bulan_ini) ?>x</h3>
                                    <h6>Transaksi Pembelian</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-info text-white">
                                <div class="card-body py-3">
                                    <h5>Barang Keluar</h5>
                                    <h3><?= number_format($penjualan_bulan_ini) ?>x</h3>
                                    <h6>Transaksi Penjualan</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aksi Cepat -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-lightning-charge"></i> Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="<?= base_url('karyawan/pembelian/create') ?>" class="btn btn-success w-100 py-3">
                                <i class="bi bi-box-seam fs-4 d-block"></i>
                                <h6 class="text-white">Barang Masuk</h6>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url('karyawan/penjualan/create') ?>" class="btn btn-info w-100 py-3">
                                <i class="bi bi-cart-plus fs-4 d-block"></i>
                                <h6 class="text-white">Barang Keluar</h6>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url('karyawan/stok') ?>" class="btn btn-warning w-100 py-3">
                                <i class="bi bi-box-seam fs-4 d-block"></i>
                                <h6 class="text-white">Manajemen Stok</h6>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url('karyawan/penjualan') ?>" class="btn btn-secondary w-100 py-3">
                                <i class="bi bi-receipt fs-4 d-block"></i>
                                <h6 class="text-white">Riwayat Penjualan</h6>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Peringatan Stok Menipis -->
    <?php if (!empty($stok_menipis)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card border-0"
                    style="border-left: 4px solid #ff9f43 !important; border-left-width: 4px !important;">
                    <div class="card-header bg-warning bg-opacity-10 border-bottom py-3 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 py-3">SKU</th>
                                        <th class="py-3">Produk</th>
                                        <th class="py-3 text-center">Stok Saat Ini</th>
                                        <th class="py-3 text-center">Min. Stok</th>
                                        <th class="py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($stok_menipis, 0, 10) as $item): ?>
                                        <tr>
                                            <td class="ps-3">
                                                <code class="text-muted small"><?= esc($item['sku']) ?></code>
                                            </td>
                                            <td><?= esc($item['nama_produk']) ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-danger"><?= number_format($item['stok']) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="text-muted"><?= number_format($item['min_stok']) ?></span>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('admin/produk/edit/' . $item['id']) ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-plus-circle me-1"></i>Tambah Stok
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                            <div class="card-footer text-center py-3 border-top">
                                <a href="<?= base_url('pemilik/laporan/stok?stok_status=menipis') ?>"
                                    class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-eye me-1"></i>Lihat Semua
                                </a>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .bg-gradient-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
    }

    .border-left-primary {
        border-left: 4px solid #4e73df !important;
    }

    .border-left-success {
        border-left: 4px solid #1cc88a !important;
    }

    .border-left-info {
        border-left: 4px solid #36b9cc !important;
    }

    .border-left-warning {
        border-left: 4px solid #f6c23e !important;
    }
</style>

<?= $this->endSection() ?>