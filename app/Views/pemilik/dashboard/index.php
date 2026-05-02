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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Produk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_produk ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto"><i class="bi bi-box-seam fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ... card lain mirip dengan admin ... -->
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
                        <small class="text-muted"><i class="bi bi-calendar"></i> Transaksi hari ini:
                            <?= $transaksi_hari_ini ?? 0 ?></small>
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
                    <div class="row" id="stockSummary">Loading...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert (tanpa tombol edit) -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">⚠️ Peringatan Stok Menipis</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($stok_menipis)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Stok</th>
                                        <th>Min Stok</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stok_menipis as $item): ?>
                                        <tr>
                                            <td><?= esc($item['nama_motif'] ?? '') ?> - <?= esc($item['nama_warna'] ?? '') ?>
                                            </td>
                                            <td><span class="badge bg-warning"><?= number_format($item['stok']) ?></span></td>
                                            <td><?= number_format($item['min_stok']) ?></td>
                                            <td><?= $item['stok'] == 0 ? 'Habis' : 'Menipis' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">Semua stok aman.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="row">
        <div class="col-xl-8 col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas 7 Hari Terakhir</h6>
                </div>
                <div class="card-body"><canvas id="weeklyChart" style="height: 300px;"></canvas></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    fetch('<?= base_url("pemilik/dashboard/getDashboardStats") ?>')
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                const ctx = document.getElementById('weeklyChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.weekly_transaksi.map(i => i.date),
                        datasets: [{
                            label: 'Jumlah Transaksi',
                            data: data.weekly_transaksi.map(i => i.total),
                            backgroundColor: '#4e73df'
                        }]
                    },
                    options: {
                        scales: { y: { beginAtZero: true, ticks: { callback: v => v + ' transaksi' } } }
                    }
                });
                document.getElementById('stockSummary').innerHTML = `<div class="col-12 text-center"><h3>${data.total_stok_quantity.toLocaleString('id-ID')}</h3><small>Total Stok (Potong)</small></div>`;
            }
        });
</script>
<?= $this->endSection() ?>