<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container">

    <!-- Welcome Banner -->
    <div class="row">
        <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #435ebe 0%, #1a2fa0 100%); border: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 text-white">Selamat Datang, <?= session()->get('username') ?>!</h4>
                            <p class="mb-0 text-white">📊 Dashboard Monitoring Bisnis Batik</p>
                        </div>
                        <div class="text-end">
                            <h5 class="mb-1">Role: <span
                                    class="badge bg-light text-dark"><?= session()->get('role') ?></span></h5>
                            <small class="text-white">📅 <?= date('l, d F Y') ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards (6 Kolom) -->
    <div class="row">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Produk</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_produk) ?>
                            </div>
                        </div>
                        <div class="col-auto"><i class="bi bi-box-seam fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Supplier</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_supplier) ?>
                            </div>
                        </div>
                        <div class="col-auto"><i class="bi bi-building fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pelanggan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_pelanggan) ?>
                            </div>
                        </div>
                        <div class="col-auto"><i class="bi bi-people fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Stok</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_stok) ?></div>
                        </div>
                        <div class="col-auto"><i class="bi bi-calculator fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Stok Menipis</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($stok_menipis) ?></div>
                        </div>
                        <div class="col-auto"><i class="bi bi-exclamation-triangle fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Stok Habis</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stok_habis) ?></div>
                        </div>
                        <div class="col-auto"><i class="bi bi-x-circle fs-2 text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik & Ringkasan Aktivitas -->
    <div class="row">
        <!-- Grafik Penjualan Bulanan -->
        <div class="col-xl-7 ">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-graph-up"></i> Aktivitas Transaksi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body py-3">
                                    <h5 class="card-title">Barang Masuk (Bulan Ini)</h5>
                                    <h3><?= number_format($pembelian_bulan_ini) ?>x</h3>
                                    <small>Transaksi Pembelian</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-info text-white mb-3">
                                <div class="card-body py-3">
                                    <h5 class="card-title">Barang Keluar (Bulan Ini)</h5>
                                    <h3><?= number_format($transaksi_bulan_ini) ?>x</h3>
                                    <small>Transaksi Penjualan</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Perbandingan dengan bulan lalu -->
                    <div class="alert alert-info mt-2 py-2">
                        <i class="bi bi-graph-up"></i>
                        <?php if ($transaksi_bulan_ini > $transaksi_bulan_lalu): ?>
                            📈 Penjualan meningkat
                            <strong><?= number_format($transaksi_bulan_ini - $transaksi_bulan_lalu) ?> transaksi</strong>
                            dibanding bulan lalu
                        <?php elseif ($transaksi_bulan_ini < $transaksi_bulan_lalu): ?>
                            📉 Penjualan menurun <strong><?= number_format($transaksi_bulan_lalu - $transaksi_bulan_ini) ?>
                                transaksi</strong> dibanding bulan lalu
                        <?php else: ?>
                            📊 Penjualan stabil dibanding bulan lalu
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aksi Cepat -->
        <div class="col-xl-5 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="bi bi-lightning-charge"></i> Navigasi Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="<?= base_url('pemilik/laporan/stok') ?>"
                                class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-box-seam fs-4 d-block"></i>
                                <small>Laporan Stok</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url('pemilik/laporan/log-stok') ?>"
                                class="btn btn-outline-info w-100 py-3">
                                <i class="bi bi-clock-history fs-4 d-block"></i>
                                <small>Log Stok</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url('pemilik/laporan/barang-masuk') ?>"
                                class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-truck fs-4 d-block"></i>
                                <small>Laporan Masuk</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url('pemilik/laporan/barang-keluar') ?>"
                                class="btn btn-outline-warning w-100 py-3">
                                <i class="bi bi-receipt fs-4 d-block"></i>
                                <small>Laporan Keluar</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Aktivitas 7 Hari Terakhir -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-bar-chart-steps"></i> Aktivitas Barang Masuk & Keluar (7 Hari Terakhir)
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" style="height: 350px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Peringatan Stok Menipis -->
    <?php if (!empty($stok_menipis)): ?>
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow border-left-warning">
                    <div class="card-header py-3 bg-warning text-white">
                        <h6 class="m-0"><i class="bi bi-exclamation-triangle"></i> Peringatan Stok Menipis</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>SKU</th>
                                        <th>Nama Produk</th>
                                        <th>Stok</th>
                                        <th>Minimal</th>
                                        <th>Rekomendasi</th>
                            </table>
                            </thead>
                            <tbody>
                                <?php foreach ($stok_menipis as $item): ?>
                                    <tr>
                                        <td><?= esc($item['sku']) ?></td>
                                        <td><?= esc($item['nama_produk']) ?></td>
                                        <td><span class="badge bg-warning text-dark"><?= number_format($item['stok']) ?></span>
                                        </td>
                                        <td><?= number_format($item['min_stok']) ?></td>
                                        <td><span class="text-danger">Segera lakukan pembelian</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
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

    .border-left-danger {
        border-left: 4px solid #e74a3b !important;
    }

    .border-left-secondary {
        border-left: 4px solid #858796 !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let activityChart = null;

    async function loadActivityChart() {
        const canvasElement = document.getElementById('activityChart');
        if (!canvasElement) {
            console.error('Element #activityChart tidak ditemukan!');
            return;
        }

        try {
            const response = await fetch('<?= base_url("pemilik/dashboard/getWeeklyActivity") ?>');
            const data = await response.json();

            const labels = data.map(item => item.date);
            const penjualanData = data.map(item => item.penjualan);
            const pembelianData = data.map(item => item.pembelian);

            if (activityChart) {
                activityChart.destroy();
            }

            const ctx = canvasElement.getContext('2d');
            activityChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Barang Keluar (Penjualan)',
                            data: penjualanData,
                            backgroundColor: 'rgba(54, 185, 204, 0.7)',
                            borderColor: '#36b9cc',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: 'Barang Masuk (Pembelian)',
                            data: pembelianData,
                            backgroundColor: 'rgba(28, 200, 138, 0.7)',
                            borderColor: '#1cc88a',
                            borderWidth: 1,
                            borderRadius: 4
                        }
                    ]
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
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Transaksi',
                                font: { size: 12 }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Tanggal',
                                font: { size: 12 }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': ' + context.raw + ' transaksi';
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error loading chart:', error);
            const container = document.getElementById('activityChart')?.parentElement;
            if (container) {
                container.innerHTML = '<div class="alert alert-warning">Gagal memuat grafik. Silakan refresh halaman.</div>';
            }
        }
    }

    // Load chart saat halaman siap
    document.addEventListener('DOMContentLoaded', function () {
        loadActivityChart();
    });
</script>

<?= $this->endSection() ?>