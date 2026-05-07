<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="page-content">
    <section class="row">
        <div class="col-12">

            <!-- Welcome Banner -->
            <div class="row">
                <div class="col-12">
                    <div class="card"
                        style="background: linear-gradient(135deg, #435ebe 0%, #1a2fa0 100%); border: none;">
                        <div class="card-body py-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="text-white mb-1 fw-semibold">
                                        Selamat Datang, <?= esc(session()->get('username')) ?>!
                                    </h5>
                                    <p class="text-white-50 mb-0 small">
                                        <i class="bi bi-calendar3 me-1"></i><?= date('l, d F Y') ?>
                                    </p>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-white text-primary px-3 py-2 fw-semibold">
                                        <i class="bi bi-person-badge me-1"></i><?= esc(session()->get('role')) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Cards -->
            <div class="row">
                <!-- Total Produk -->
                <div class="col-6 col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center py-3 px-3">
                            <div class="stats-icon bg-primary bg-opacity-10 rounded-3 me-3 d-flex align-items-center justify-content-center"
                                style="width:48px;height:48px;flex-shrink:0;">
                                <i class="bi bi-box-seam text-primary fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-muted mb-0" style="font-size:0.72rem;white-space:nowrap;">Total Produk
                                </p>
                                <h5 class="mb-0 fw-bold"><?= number_format($total_produk) ?></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Stok -->
                <div class="col-6 col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center py-3 px-3">
                            <div class="stats-icon bg-success bg-opacity-10 rounded-3 me-3 d-flex align-items-center justify-content-center"
                                style="width:48px;height:48px;flex-shrink:0;">
                                <i class="bi bi-layers text-success fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-muted mb-0" style="font-size:0.72rem;white-space:nowrap;">Total Stok</p>
                                <h5 class="mb-0 fw-bold"><?= number_format($total_stok) ?></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Penjualan Bulan Ini -->
                <div class="col-6 col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center py-3 px-3">
                            <div class="stats-icon bg-info bg-opacity-10 rounded-3 me-3 d-flex align-items-center justify-content-center"
                                style="width:48px;height:48px;flex-shrink:0;">
                                <i class="bi bi-receipt text-info fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-muted mb-0" style="font-size:0.72rem;white-space:nowrap;">Penjualan
                                    (Bulan ini)</p>
                                <h5 class="mb-0 fw-bold"><?= number_format($transaksi_bulan_ini) ?><small
                                        class="fs-6 text-muted fw-normal">x</small></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pembelian Bulan Ini -->
                <div class="col-6 col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center py-3 px-3">
                            <div class="stats-icon bg-warning bg-opacity-10 rounded-3 me-3 d-flex align-items-center justify-content-center"
                                style="width:48px;height:48px;flex-shrink:0;">
                                <i class="bi bi-truck text-warning fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-muted mb-0" style="font-size:0.72rem;white-space:nowrap;">Pembelian
                                    (Bulan ini)</p>
                                <h5 class="mb-0 fw-bold"><?= number_format($pembelian_bulan_ini) ?><small
                                        class="fs-6 text-muted fw-normal">x</small></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stok Menipis -->
                <div class="col-6 col-md-4 mb-3">
                    <div class="card h-100 <?= count($stok_menipis) > 0 ? 'border-danger' : '' ?>">
                        <div class="card-body d-flex align-items-center py-3 px-3">
                            <div class="stats-icon bg-danger bg-opacity-10 rounded-3 me-3 d-flex align-items-center justify-content-center"
                                style="width:48px;height:48px;flex-shrink:0;">
                                <i class="bi bi-exclamation-triangle text-danger fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-muted mb-0" style="font-size:0.72rem;white-space:nowrap;">Stok Menipis
                                </p>
                                <h5 class="mb-0 fw-bold <?= count($stok_menipis) > 0 ? 'text-danger' : '' ?>">
                                    <?= count($stok_menipis) ?>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stok Habis -->
                <div class="col-6 col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center py-3 px-3">
                            <div class="stats-icon bg-secondary bg-opacity-10 rounded-3 me-3 d-flex align-items-center justify-content-center"
                                style="width:48px;height:48px;flex-shrink:0;">
                                <i class="bi bi-x-circle text-secondary fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-muted mb-0" style="font-size:0.72rem;white-space:nowrap;">Stok Habis</p>
                                <h5 class="mb-0 fw-bold"><?= number_format($stok_habis) ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik & Aksi Cepat -->
            <div class="row mb-4">
                <!-- Grafik Penjualan -->
                <div class="col-xl-8 col-12 mb-4 mb-xl-0">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Penjualan 7 Hari Terakhir
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="weeklySalesChart" style="height: 300px; width: 100%;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Aksi Cepat -->
                <div class="col-xl-4 col-12">
                    <div class="card h-100">
                        <div class="card-header border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="bi bi-lightning-charge me-2 text-primary"></i>Aksi Cepat
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="<?= base_url('admin/produk/create') ?>"
                                        class="btn btn-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-1 text-decoration-none">
                                        <i class="bi bi-plus-circle fs-4 mb-3"></i>
                                        <small class="fw-semibold">Tambah Produk</small>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="<?= base_url('karyawan/pembelian/create') ?>"
                                        class="btn btn-success w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-1 text-decoration-none">
                                        <i class="bi bi-box-seam fs-4 mb-3"></i>
                                        <small class="fw-semibold">Barang Masuk</small>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="<?= base_url('karyawan/penjualan/create') ?>"
                                        class="btn btn-info w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-1 text-decoration-none">
                                        <i class="bi bi-cart-plus fs-4 mb-3"></i>
                                        <small class="fw-semibold">Barang Keluar</small>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="<?= base_url('admin/laporan/stok') ?>"
                                        class="btn btn-warning w-100 py-3 d-flex flex-column align-items-center justify-content-center gap-1 text-decoration-none">
                                        <i class="bi bi-file-earmark-bar-graph fs-4 mb-3"></i>
                                        <small class="fw-semibold">Laporan Stok</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Produk & Aktivitas Terbaru -->
            <div class="row mb-4">
                <!-- Top 5 Produk Terlaris -->
                <div class="col-xl-6 col-12 mb-4 mb-xl-0">
                    <div class="card h-100">
                        <div class="card-header border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="bi bi-trophy me-2 text-warning"></i>Top 5 Produk Terlaris
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($top_produk)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-3 py-3" style="width:40px;">#</th>
                                                <th class="py-3">Motif</th>
                                                <th class="py-3">Warna</th>
                                                <th class="py-3 text-end pe-3">Terjual</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($top_produk as $i => $item): ?>
                                                <tr>
                                                    <td class="ps-3">
                                                        <?php if ($i === 0): ?>
                                                            <span class="badge bg-warning text-dark">1</span>
                                                        <?php elseif ($i === 1): ?>
                                                            <span class="badge bg-secondary">2</span>
                                                        <?php elseif ($i === 2): ?>
                                                            <span class="badge" style="background:#cd7f32;">3</span>
                                                        <?php else: ?>
                                                            <span class="text-muted small"><?= $i + 1 ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= esc($item['nama_motif']) ?></td>
                                                    <td>
                                                        <span
                                                            class="badge bg-light text-dark border"><?= esc($item['nama_warna']) ?></span>
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <span
                                                            class="fw-semibold text-primary"><?= number_format($item['total_terjual']) ?></span>
                                                        <span class="text-muted small"> pcs</span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center py-5 text-muted">
                                    <div class="text-center">
                                        <i class="bi bi-bar-chart fs-1 opacity-25 d-block mb-2"></i>
                                        <small>Belum ada data penjualan</small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Aktivitas Terbaru -->
                <div class="col-xl-6 col-12">
                    <div class="card h-100">
                        <div class="card-header border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="bi bi-clock-history me-2 text-primary"></i>Aktivitas Terbaru
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($aktivitas_terbaru)): ?>
                                <div class="d-flex flex-column gap-3">
                                    <?php foreach ($aktivitas_terbaru as $aktivitas): ?>
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width:36px;height:36px;background:<?= $aktivitas['tipe'] == 'pembelian' ? 'rgba(40,199,111,0.15)' : 'rgba(0,207,232,0.15)' ?>">
                                                <i
                                                    class="<?= $aktivitas['tipe'] == 'pembelian' ? 'bi bi-truck text-success' : 'bi bi-receipt text-info' ?>"></i>
                                            </div>
                                            <div class="flex-grow-1 overflow-hidden">
                                                <p class="mb-0 fw-semibold text-truncate small">
                                                    <?= esc($aktivitas['deskripsi']) ?>
                                                </p>
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    <span class="text-muted" style="font-size:0.72rem;">
                                                        <i
                                                            class="bi bi-calendar3 me-1"></i><?= date('d/m/Y H:i', strtotime($aktivitas['tanggal'])) ?>
                                                    </span>
                                                    <span class="badge bg-light text-secondary border"
                                                        style="font-size:0.65rem;"><?= esc($aktivitas['ref']) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center py-5 text-muted">
                                    <div class="text-center">
                                        <i class="bi bi-clock fs-1 opacity-25 d-block mb-2"></i>
                                        <small>Belum ada aktivitas</small>
                                    </div>
                                </div>
                            <?php endif; ?>
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
                            <div
                                class="card-header bg-warning bg-opacity-10 border-bottom py-3 d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                <h6 class="mb-0 fw-semibold text-warning">Peringatan: <?= count($stok_menipis) ?> Produk
                                    Stok Menipis</h6>
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
                                            <?php foreach ($stok_menipis as $item): ?>
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
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Load chart data
    async function loadChart() {
        // Cek apakah element canvas ada
        const canvasElement = document.getElementById('weeklySalesChart');
        if (!canvasElement) {
            console.error('Element #weeklySalesChart tidak ditemukan di halaman!');
            return;
        }

        try {
            const response = await fetch('<?= base_url("admin/dashboard/getWeeklySales") ?>');
            const data = await response.json();

            console.log('API Response:', data);

            // Pastikan data adalah array
            let chartData = [];
            let chartLabels = [];

            if (Array.isArray(data)) {
                chartLabels = data.map(item => item.date);
                chartData = data.map(item => item.total);
            } else if (data.data && Array.isArray(data.data)) {
                chartLabels = data.data.map(item => item.date);
                chartData = data.data.map(item => item.total);
            } else {
                console.warn('Data format tidak sesuai:', data);
                // Data dummy jika kosong
                for (let i = 6; i >= 0; i--) {
                    const d = new Date();
                    d.setDate(d.getDate() - i);
                    chartLabels.push(d.getDate() + '/' + (d.getMonth() + 1));
                    chartData.push(0);
                }
            }

            const ctx = canvasElement.getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Jumlah Transaksi',
                        data: chartData,
                        backgroundColor: 'rgba(78, 115, 223, 0.5)',
                        borderColor: '#4e73df',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error loading chart:', error);
            const container = document.getElementById('weeklySalesChart')?.parentElement;
            if (container) {
                container.innerHTML = '<div class="alert alert-warning">Gagal memuat grafik. Silakan refresh halaman.</div>';
            }
        }
    }

    // Jalankan setelah DOM selesai dimuat
    document.addEventListener('DOMContentLoaded', loadChart);
</script>

<?= $this->endSection() ?>