<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-receipt"></i> Data Transaksi Penjualan</h5>
        </div>
        <div class="card-body p-3">
            <div class="mb-3">
                <a href="<?= base_url('kasir/penjualan/create') ?>" class="btn btn-success">
                    <i class="bi bi-plus"></i> Transaksi Baru
                </a>
            </div>

            <!-- Filter Form -->
            <form method="GET" class="row mb-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Cari No Invoice..."
                        value="<?= esc($search ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="tipe_pembayaran" class="form-select">
                        <option value="">Semua Tipe</option>
                        <option value="tunai" <?= ($tipe_pembayaran ?? '') == 'tunai' ? 'selected' : '' ?>>Tunai</option>
                        <option value="transfer" <?= ($tipe_pembayaran ?? '') == 'transfer' ? 'selected' : '' ?>>Transfer</option>
                        <option value="qris" <?= ($tipe_pembayaran ?? '') == 'qris' ? 'selected' : '' ?>>QRIS</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="selesai" <?= ($status ?? '') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        <option value="batal" <?= ($status ?? '') == 'batal' ? 'selected' : '' ?>>Batal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="start_date" class="form-control" placeholder="Start Date"
                        value="<?= esc($start_date ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <input type="date" name="end_date" class="form-control" placeholder="End Date"
                        value="<?= esc($end_date ?? '') ?>">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <!-- Reset Filter -->
            <?php if (!empty($search) || !empty($tipe_pembayaran) || !empty($status) || !empty($start_date) || !empty($end_date)): ?>
                <div class="mb-3">
                    <a href="<?= base_url('kasir/penjualan') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Reset Filter
                    </a>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tableTransaksi">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No Invoice</th>
                            <th>Tanggal</th>
                            <th class="text-end">Total Bayar</th>
                            <th>Tipe Bayar</th>
                            <th>Status</th>
                            <th>Sisa Waktu Batal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php if (!empty($transaksi) && is_array($transaksi)): ?>
                            <?php foreach ($transaksi as $item): ?>
                                <?php 
                                // Tentukan class badge untuk tipe pembayaran
                                $badgeClass = $item['tipe_pembayaran'] == 'tunai' ? 'success' : ($item['tipe_pembayaran'] == 'transfer' ? 'info' : 'primary');
                                ?>
                                <tr>
                                    <td><strong><?= esc($item['no_invoice']) ?></strong></td>
                                    <td><?= date('d-m-Y H:i', strtotime($item['created_at'])) ?></td>
                                    <td class="text-end">Rp <?= number_format($item['total_bayar'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $badgeClass ?>"><?= strtoupper($item['tipe_pembayaran']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($item['status'] == 'selesai'): ?>
                                            <span class="badge bg-success">Selesai</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Batal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="<?= isset($item['can_cancel']) && $item['can_cancel'] ? 'text-warning' : 'text-danger' ?>">
                                        <?= $item['remaining_text'] ?? '-' ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="showStruk(<?= $item['id'] ?>)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-secondary" onclick="showStrukModal(<?= $item['id'] ?>)">
                                            <i class="bi bi-receipt"></i>
                                        </button>
                                        <?php if ($item['status'] == 'selesai'): ?>
                                            <?php if (isset($item['can_cancel']) && $item['can_cancel']): ?>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="confirmCancel(<?= $item['id'] ?>, '<?= esc($item['no_invoice']) ?>', <?= $item['remaining_minutes'] ?? 0 ?>)">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled
                                                    title="Melebihi batas waktu pembatalan (60 menit)">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    <?php 
                                    if (!empty($search) || !empty($tipe_pembayaran) || !empty($status) || !empty($start_date) || !empty($end_date)) {
                                        echo 'Tidak ada transaksi yang sesuai dengan filter';
                                    } else {
                                        echo 'Belum ada transaksi';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($pager) && $pager && $pager->getTotal() > 0): ?>
                <div class="mt-4 d-flex justify-content-center">
                    <?= $pager->links('default', 'bootstrap_pagination') ?>
                </div>
                <div class="mt-3 text-muted text-center small">
                    Menampilkan <?= count($transaksi) ?> dari <?= number_format($total ?? 0) ?> data
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Struk -->
<div class="modal fade" id="strukModal" tabindex="-1" aria-labelledby="strukModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="strukModalLabel">
                    <i class="bi bi-receipt"></i> Struk Pembayaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="strukContent" class="p-3">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Memuat struk...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary" onclick="printStruk()">
                    <i class="bi bi-printer"></i> Cetak
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Struk Otomatis (setelah transaksi) -->
<?php if (session()->getFlashdata('show_struk')): ?>
    <?php $struk = session()->getFlashdata('struk_data'); ?>
    <?php if ($struk): ?>
        <div class="modal fade" id="autoStrukModal" tabindex="-1" aria-labelledby="autoStrukModalLabel" aria-hidden="true"
            data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-receipt"></i> Struk Pembayaran
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div id="strukContentAuto" class="struk p-3"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Tutup
                        </button>
                        <button type="button" class="btn btn-primary" onclick="printAutoStruk()">
                            <i class="bi bi-printer"></i> Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const autoStrukData = <?= json_encode($struk) ?>;
            document.getElementById('strukContentAuto').innerHTML = generateStrukHTML(autoStrukData);
            var autoModal = new bootstrap.Modal(document.getElementById('autoStrukModal'));
            autoModal.show();

            function printAutoStruk() {
                const printContent = document.getElementById('strukContentAuto').innerHTML;
                const printWindow = window.open('', '_blank');
                printWindow.document.write(getPrintHtml(printContent));
                printWindow.document.close();
            }
        </script>
    <?php endif; ?>
<?php endif; ?>

<script>
    // Fungsi untuk mengambil data struk via AJAX
    function showStrukModal(id) {
        const modal = new bootstrap.Modal(document.getElementById('strukModal'));
        document.getElementById('strukContent').innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Memuat struk...</p>
            </div>
        `;
        modal.show();

        fetch(`<?= base_url('kasir/penjualan/get-struk-data/') ?>${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('strukContent').innerHTML = generateStrukHTML(data.data);
                } else {
                    document.getElementById('strukContent').innerHTML = `
                        <div class="alert alert-danger m-3">
                            <i class="bi bi-exclamation-triangle"></i> ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                document.getElementById('strukContent').innerHTML = `
                    <div class="alert alert-danger m-3">
                        <i class="bi bi-exclamation-triangle"></i> Gagal memuat struk
                    </div>
                `;
            });
    }

    function getPrintHtml(content) {
        return `<!DOCTYPE html>
            <html>
            <head>
                <title>Struk Pembayaran</title>
                <style>
                    body { font-family: monospace; margin: 0; padding: 10px; }
                    .struk { max-width: 350px; margin: 0 auto; }
                    .text-center { text-align: center; }
                    .text-end { text-align: right; }
                    .fw-bold { font-weight: bold; }
                    hr { border: 1px dashed #000; margin: 10px 0; }
                    table { width: 100%; font-size: 12px; }
                    td { padding: 2px 0; }
                </style>
            </head>
            <body>${content}
            <script>
                window.onload = function() { window.print(); setTimeout(() => window.close(), 500); };
            <\/script>
            </body>
            </html>`;
    }

    function generateStrukHTML(data) {
        let detailHtml = '';
        if (data.detail && data.detail.length > 0) {
            data.detail.forEach(item => {
                detailHtml += `
                    <tr>
                        <td>${item.nama_produk}</td>
                        <td class="text-end">${item.jumlah}</td>
                        <td class="text-end">${formatRupiah(item.harga_satuan)}</td>
                        <td class="text-end">${formatRupiah(item.subtotal)}</td>
                    </tr>
                `;
            });
        }

        return `
            <div class="struk">
                <div class="text-center mb-3">
                    <h5 class="mb-0">OMAH NINI</h5>
                    <small>Jl. Contoh No. 123, Kota</small><br>
                    <small>Telp: 0812-3456-7890</small>
                </div>
                <hr>
                <div>
                    <div>No Invoice: ${data.transaksi.no_invoice}</div>
                    <div>Tanggal: ${formatTanggal(data.transaksi.created_at)}</div>
                    <div>Kasir: ${data.kasir || '-'}</div>
                    <div>Tipe Bayar: ${(data.transaksi.tipe_pembayaran || '').toUpperCase()}</div>
                </div>
                <hr>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Harga</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>${detailHtml}</tbody>
                    <tfoot>
                        <tr><td colspan="3" class="text-end fw-bold">Total</td>
                        <td class="text-end fw-bold">${formatRupiah(data.total_belanja)}</td>
                    </tr>
                        <tr><td colspan="3" class="text-end">Bayar</td>
                        <td class="text-end">${formatRupiah(data.transaksi.total_bayar)}</td>
                    </tr>
                        <tr><td colspan="3" class="text-end">Kembalian</td>
                        <td class="text-end">${formatRupiah(data.kembalian)}</td>
                    </tr>
                    </tfoot>
                </table>
                <hr>
                <div class="text-center">
                    <small>Terima kasih atas kunjungan Anda!</small><br>
                    <small>*** SIMPAN STRUK INI SEBAGAI BUKTI ***</small>
                </div>
            </div>
        `;
    }

    function formatRupiah(angka) {
        if (!angka) return 'Rp 0';
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    function formatTanggal(tanggal) {
        if (!tanggal) return '-';
        const d = new Date(tanggal);
        return d.toLocaleDateString('id-ID') + ' ' + d.toLocaleTimeString('id-ID');
    }

    function printStruk() {
        const printContent = document.getElementById('strukContent').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(getPrintHtml(printContent));
        printWindow.document.close();
    }

    function showStruk(id) {
        window.open(`<?= base_url('kasir/penjualan/struk/') ?>${id}`, '_blank');
    }

    function confirmCancel(id, noInvoice, remainingMinutes) {
        let hours = Math.floor(remainingMinutes / 60);
        let minutes = remainingMinutes % 60;
        let timeText = '';

        if (hours > 0) {
            timeText = `${hours} jam ${minutes} menit`;
        } else {
            timeText = `${minutes} menit`;
        }

        Swal.fire({
            title: 'Konfirmasi Pembatalan',
            html: `Apakah Anda yakin ingin membatalkan transaksi <strong>${noInvoice}</strong>?<br><br>
                   <span style="color: orange;">⚠️ Anda memiliki waktu ${timeText} untuk membatalkan.</span><br><br>
                   Stok akan dikembalikan dan keuangan akan disesuaikan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?= base_url('kasir/penjualan/batal/') ?>${id}`;
            }
        });
    }
</script>

<style>
    .struk {
        font-family: monospace;
        font-size: 12px;
    }
    .struk hr {
        margin: 8px 0;
        border: 1px dashed #dee2e6;
    }
    .struk table {
        width: 100%;
    }
    .struk td, .struk th {
        padding: 2px 0;
    }
    .pagination {
        justify-content: center;
    }
    .page-link {
        color: #4f46e5;
    }
    .page-item.active .page-link {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
</style>
<?= $this->endSection() ?>