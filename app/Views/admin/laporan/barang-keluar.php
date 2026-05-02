<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h4><?= $title ?></h4>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="get" class="row mb-4">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="<?= $tanggalMulai ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" class="form-control" value="<?= $tanggalAkhir ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Pelanggan</label>
                <select name="pelanggan" class="form-select">
                    <option value="">-- Semua --</option>
                    <?php foreach ($pelanggan_list as $pel): ?>
                        <option value="<?= $pel['id'] ?>" <?= $selectedPelanggan == $pel['id'] ? 'selected' : '' ?>>
                            <?= esc($pel['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-funnel"></i> Filter</button>
                <a href="<?= base_url('admin/laporan/barang-keluar') ?>" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <!-- Tabel -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Pembeli</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Jumlah Item</th>
                        <th>Detail Item</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($transaksi)): ?>
                    <?php $no = 1 + (($pager->getCurrentPage() - 1) * $pager->getPerPage()); ?>
                    <?php foreach ($transaksi as $trx): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= esc($trx['no_invoice']) ?></td>
                            <td><?= esc($trx['nama_pembeli']) ?></td>
                            <td><?= esc($trx['nama_pelanggan'] ?? '-') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($trx['tanggal_transaksi'])) ?></td>
                            <td class="text-center">
                                <?php
                                $detailModel = new \App\Models\DetailTransaksiModel();
                                $items = $detailModel->getByTransaksiId($trx['id']);
                                echo count($items);
                                ?>
                            </td>
                            <td>
                                <?php if (!empty($items)): ?>
                                    <ul class="list-unstyled mb-0">
                                        <?php foreach ($items as $item): ?>
                                            <li><?= esc($item['nama_produk']) ?> (<?= $item['jumlah'] ?> pcs)</li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= esc($trx['catatan'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data penjualan.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= $pager ? $pager->links('default', 'bootstrap_pagination') : '' ?>
    </div>
</div>

<?= $this->endSection() ?>