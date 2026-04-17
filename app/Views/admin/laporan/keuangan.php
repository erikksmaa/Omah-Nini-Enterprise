<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-graph-up"></i> Laporan Keuangan</h5>
        </div>
        <div class="card-body p-3 p-3">
            <!-- Filter Form -->
            <form method="GET" class="row mb-4">
                <div class="col-md-3">
                    <label>Bulan</label>
                    <select name="bulan" class="form-control">
                        <?php foreach ($bulan_list as $key => $val): ?>
                            <option value="<?= $key ?>" <?= $bulan == $key ? 'selected' : '' ?>><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Tahun</label>
                    <select name="tahun" class="form-control">
                        <?php foreach ($tahun_list as $key => $val): ?>
                            <option value="<?= $key ?>" <?= $tahun == $key ? 'selected' : '' ?>><?= $val ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-success w-100" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                </div>
            </form>

            <!-- Ringkasan -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-white bg-success">
                        <div class="card-body p-3">
                            <h6>Total Pemasukan</h6>
                            <h3>Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-danger">
                        <div class="card-body p-3">
                            <h6>Total Pengeluaran</h6>
                            <h3>Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-<?= $laba_rugi >= 0 ? 'secondary' : 'warning' ?>">
                        <div class="card-body p-3">
                            <h6>Laba/Rugi</h6>
                            <h3>Rp <?= number_format(abs($laba_rugi), 0, ',', '.') ?></h3>
                            <small><?= $laba_rugi >= 0 ? 'Untung' : 'Rugi' ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Pemasukan per Kategori -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">Pemasukan per Kategori</h6>
                        </div>
                        <div class="card-body p-3 p-3">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr><th>Kategori</th><th class="text-end">Total</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pemasukan_by_kategori as $item): ?>
                                            <tr>
                                                <td><?= ucfirst($item['kategori']) ?></td>
                                                <td class="text-end">Rp <?= number_format($item['total'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($pemasukan_by_kategori)): ?>
                                            <tr><td colspan="2" class="text-center">-</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Pengeluaran per Kategori -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">Pengeluaran per Kategori</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr><th>Kategori</th><th class="text-end">Total</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pengeluaran_by_kategori as $item): ?>
                                            <tr>
                                                <td><?= ucfirst($item['kategori']) ?></td>
                                                <td class="text-end">Rp <?= number_format($item['total'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($pengeluaran_by_kategori)): ?>
                                            <tr><td colspan="2" class="text-center">-</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Transaksi -->
            <div class="card mt-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Detail Transaksi Keuangan</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tableKeuangan">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Kategori</th>
                                    <th>Referensi</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detail as $item): ?>
                                    <tr>
                                        <td><?= date('d-m-Y', strtotime($item['tanggal_transaksi'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $item['tipe'] == 'pemasukan' ? 'success' : 'danger' ?>">
                                                <?= $item['tipe'] == 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' ?>
                                            </span>
                                        </td>
                                        <td><?= ucfirst($item['kategori']) ?></td>
                                        <td><?= $item['tipe_ref'] ?> #<?= $item['id_ref'] ?></td>
                                        <td class="text-end">Rp <?= number_format($item['jumlah'], 0, ',', '.') ?></td>
                                        <td><?= $item['username'] ?? '-' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($detail)): ?>
                                    <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tableKeuangan').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            order: [[0, 'desc']],
            pageLength: 25
        });
    });
</script>
<?= $this->endSection() ?>