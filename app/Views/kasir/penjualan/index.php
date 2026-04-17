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

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped" id="tableTransaksi">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>No Invoice</th>
                            <th>Tanggal</th>
                            <th>Total Bayar</th>
                            <th>Tipe Bayar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php foreach ($transaksi as $item): ?>
                            <tr>
                                <td><strong><?= $item['no_invoice'] ?></strong></td>
                                <td><?= date('d-m-Y H:i', strtotime($item['created_at'])) ?></td>
                                <td>Rp <?= number_format($item['total_bayar'], 0, ',', '.') ?></td>
                                <td><?= strtoupper($item['tipe_pembayaran']) ?></td>
                                <td>
                                    <?php if ($item['status'] == 'selesai'): ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Batal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="showStruk(<?= $item['id'] ?>)">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="showStrukModal(<?= $item['id'] ?>)">
                                        <i class="bi bi-receipt"></i> Struk
                                    </button>
                                    <?php if ($item['status'] == 'selesai'): ?>
                                        <a href="<?= base_url('kasir/penjualan/batal/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Yakin membatalkan transaksi ini? Stok akan dikembalikan.')">
                                            <i class="bi bi-x-circle"></i> Batal
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($transaksi)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada transaksi</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
        <div class="modal fade" id="autoStrukModal" tabindex="-1" aria-labelledby="autoStrukModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-receipt"></i> Struk Pembayaran
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div id="strukContentAuto" class="struk p-3">
                            <!-- Struk content akan diisi -->
                        </div>
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
            // Isi struk otomatis
            const autoStrukData = <?= json_encode($struk) ?>;
            document.getElementById('strukContentAuto').innerHTML = generateStrukHTML(autoStrukData);
            
            // Tampilkan modal otomatis
            var autoModal = new bootstrap.Modal(document.getElementById('autoStrukModal'));
            autoModal.show();
            
            function printAutoStruk() {
                const printContent = document.getElementById('strukContentAuto').innerHTML;
                const printWindow = window.open('', '_blank');
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Struk Pembayaran</title>
                        <style>
                            body {
                                font-family: monospace;
                                margin: 0;
                                padding: 10px;
                            }
                            .struk {
                                max-width: 350px;
                                margin: 0 auto;
                            }
                            .text-center { text-align: center; }
                            .text-end { text-align: right; }
                            .fw-bold { font-weight: bold; }
                            hr {
                                border: 1px dashed #000;
                                margin: 10px 0;
                            }
                            table {
                                width: 100%;
                                font-size: 12px;
                            }
                            td {
                                padding: 2px 0;
                            }
                        </style>
                    </head>
                    <body>
                        ${printContent}
                        <script>
                            window.onload = function() {
                                window.print();
                                setTimeout(() => window.close(), 500);
                            };
                        <\/script>
                    </body>
                    </html>
                `);
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
    
    // Generate HTML struk
    function generateStrukHTML(data) {
        let detailHtml = '';
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
                    <div>Kasir: ${data.kasir}</div>
                    <div>Tipe Bayar: ${data.transaksi.tipe_pembayaran.toUpperCase()}</div>
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
                    <tbody>
                        ${detailHtml}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold">${formatRupiah(data.total_belanja)}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">Bayar</td>
                            <td class="text-end">${formatRupiah(data.transaksi.total_bayar)}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end">Kembalian</td>
                            <td class="text-end">${formatRupiah(data.kembalian)}</td>
                        </tr>
                    </tfoot>
                </table>
                <hr>
                <div class="text-center">
                    <small>Terima kasih atas kunjungan Anda!</small><br>
                    <small>Barang yang sudah dibeli tidak dapat dikembalikan</small>
                    <hr>
                    <small>*** SIMPAN STRUK INI SEBAGAI BUKTI ***</small>
                </div>
            </div>
        `;
    }
    
    // Format Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }
    
    // Format Tanggal
    function formatTanggal(tanggal) {
        const d = new Date(tanggal);
        return d.toLocaleDateString('id-ID') + ' ' + d.toLocaleTimeString('id-ID');
    }
    
    // Fungsi print
    function printStruk() {
        const printContent = document.getElementById('strukContent').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Struk Pembayaran</title>
                <style>
                    body {
                        font-family: monospace;
                        margin: 0;
                        padding: 10px;
                    }
                    .struk {
                        max-width: 350px;
                        margin: 0 auto;
                    }
                    .text-center { text-align: center; }
                    .text-end { text-align: right; }
                    .fw-bold { font-weight: bold; }
                    hr {
                        border: 1px dashed #000;
                        margin: 10px 0;
                    }
                    table {
                        width: 100%;
                        font-size: 12px;
                    }
                    td, th {
                        padding: 2px 0;
                    }
                </style>
            </head>
            <body>
                ${printContent}
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(() => window.close(), 500);
                    };
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
    
    function showStruk(id) {
        window.open(`<?= base_url('kasir/penjualan/struk/') ?>${id}`, '_blank');
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
</style>
<?= $this->endSection() ?>