<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Statistik Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Produk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_produk ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box-seam fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Supplier / Brand</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($total_supplier ?? 0) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-building fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Motif & Warna</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format(($total_motif ?? 0) + ($total_warna ?? 0)) ?></div>
                            <small>Motif: <?= $total_motif ?? 0 ?> | Warna: <?= $total_warna ?? 0 ?></small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-palette fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                User & Pelanggan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format(($total_user ?? 0) + ($total_pelanggan ?? 0)) ?></div>
                            <small>User: <?= $total_user ?? 0 ?> | Pelanggan: <?= $total_pelanggan ?? 0 ?></small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Bulan Ini -->
    <div class="row">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Bulan Ini</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 text-center">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body py-3">
                                    <h5 class="card-title">Barang Masuk</h5>
                                    <h3><?= number_format($pembelian_bulan_ini ?? 0) ?></h3>
                                    <small>Transaksi Pembelian</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="card bg-info text-white mb-3">
                                <div class="card-body py-3">
                                    <h5 class="card-title">Barang Keluar</h5>
                                    <h3><?= number_format($penjualan_bulan_ini ?? 0) ?></h3>
                                    <small>Transaksi Penjualan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> Transaksi hari ini: <?= $transaksi_hari_ini ?? 0 ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Stok</h6>
                </div>
                <div class="card-body">
                    <div class="row" id="stockSummary">
                        <div class="col-12 text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="bi bi-exclamation-triangle"></i> Peringatan Stok Menipis
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($stok_menipis) && count($stok_menipis) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Stok Saat Ini</th>
                                        <th>Stok Minimal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stok_menipis as $item): ?>
                                        <tr>
                                            <td><?= $item['nama_produk'] ?></td>
                                            <td><span
                                                    class="badge bg-warning text-dark"><?= number_format($item['stok']) ?></span>
                                            </td>
                                            <td><?= number_format($item['min_stok']) ?></span></td>
                                            <td>
                                                <?php if ($item['stok'] == 0): ?>
                                                    <span class="badge bg-danger">Habis</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Menipis</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('admin/produk/edit/' . $item['id']) ?>"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i> Edit Stok
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

    <!-- Chart Section -->
    <div class="row">
        <div class="col-xl-8 col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas 7 Hari Terakhir</h6>
                </div>
                <div class="card-body">
                    <canvas id="weeklyChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Load chart data
    fetch('<?= base_url("admin/dashboard/getDashboardStats") ?>')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Weekly Chart (jumlah transaksi)
                const ctx = document.getElementById('weeklyChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.weekly_transaksi.map(item => item.date),
                        datasets: [{
                            label: 'Jumlah Transaksi',
                            data: data.weekly_transaksi.map(item => item.total),
                            backgroundColor: '#4e73df',
                            borderColor: '#4e73df',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function (value) {
                                        return value + ' transaksi';
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return context.raw + ' transaksi';
                                    }
                                }
                            }
                        }
                    }
                });

                // Stock Summary
                const stockHtml = `
                    <div class="col-6 text-center">
                        <div class="card bg-light mb-2">
                            <div class="card-body py-3">
                                <h6 class="text-muted">Total Stok</h6>
                                <h3 class="text-primary">${data.total_stok_quantity.toLocaleString('id-ID')}</h3>
                                <small>Potong</small>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('stockSummary').innerHTML = stockHtml;
            }
        })
        .catch(error => console.error('Error loading chart data:', error));

    // Dark mode handling (jika ada)
    function toggleDarkMode() {
        document.body.classList.toggle('dark');
    }
</script>

<?= $this->endSection() ?>