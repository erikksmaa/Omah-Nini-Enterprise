<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h4>Tambah Penjualan</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('karyawan/penjualan/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="datetime-local" name="tanggal_transaksi" class="form-control"
                        value="<?= old('tanggal_transaksi', date('Y-m-d\TH:i')) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pelanggan (opsional)</label>
                    <select name="id_pelanggan" id="pelanggan" class="form-select">
                        <option value="">-- Umum --</option>
                        <?php foreach ($pelanggan_list as $pel): ?>
                            <option value="<?= $pel['id'] ?>" data-nama="<?= esc($pel['nama']) ?>" <?= old('id_pelanggan') == $pel['id'] ? 'selected' : '' ?>><?= esc($pel['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nama Pembeli</label>
                    <input type="text" name="nama_pembeli" id="nama_pembeli" class="form-control"
                        value="<?= old('nama_pembeli') ?>" required>
                    <small class="text-muted">Jika pelanggan dipilih, nama otomatis terisi.</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2"><?= old('catatan') ?></textarea>
                </div>
            </div>

            <hr>
            <h5>Item Produk</h5>
            <div id="items-container">
                <!-- JS akan mengisi baris item -->
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3 mt-3 flex-wrap gap-2">
                <div>
                    <button type="button" id="btnAddItem" class="btn btn-outline-primary">
                        <i class="bi bi-plus"></i> Tambah Produk
                    </button>
                    <small id="itemCount" class="text-muted d-block mt-2">Item: <span>0</span></small>
                </div>
                <div class="text-end">
                    <small class="text-muted">Total Keseluruhan:</small>
                    <h4 class="mb-0 text-primary" id="totalKeseluruhan">Rp 0,00</h4>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success"><i class="bi bi-cart-check"></i> Proses Penjualan</button>
                <a href="<?= base_url('karyawan/penjualan') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<!-- Template Baris Item -->
<template id="itemTemplate">
    <div class="item-row border rounded p-3 mb-3">
        <div class="row g-2">
            <!-- Kolom Produk + Stok -->
            <div class="col-lg-6">
                <div>
                    <label class="form-label">Produk <span class="text-danger">*</span></label>
                    <select name="items[INDEX][id_produk]" class="form-select produk-select" required>
                        <option value="">-- Cari Produk (SKU - Motif - Warna) --</option>
                    </select>
                    <!-- Stok Info -->
                    <div class="stok-info alert alert-info mt-2 mb-0 py-2 px-3" style="display: none;">
                        <small>
                            <strong>Stok Tersedia:</strong> <span class="stok-badge badge bg-success">0</span>
                        </small>
                    </div>
                </div>
                <!-- Preview foto produk -->
                <div class="mt-2 foto-preview" style="display: none;">
                    <img class="foto-img" src="" alt="Foto Produk" style="max-height: 100px; cursor: pointer;">
                    <span class="small text-muted ms-2 d-block mt-1">Klik gambar untuk memperbesar</span>
                </div>
            </div>
            
            <!-- Kolom Jumlah, Harga, Subtotal, Hapus -->
            <div class="col-lg-6">
                <div class="row g-2 align-items-end h-100">
                    <div class="col-sm-3">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="items[INDEX][jumlah]" class="form-control jumlah" 
                            placeholder="Qty" min="1" required>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" name="items[INDEX][harga_satuan]" class="form-control harga-satuan"
                                placeholder="0" required>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label">Subtotal</label>
                        <input type="text" class="form-control subtotal" readonly>
                    </div>
                    <div class="col-sm-1">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-item w-100" 
                            title="Hapus item">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Modal Zoom -->
<div class="modal fade" id="zoomModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">Foto Produk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="zoomImage" src="" style="max-width: 100%; max-height: 70vh;">
            </div>
        </div>
    </div>
</div>



<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('items-container');
        const template = document.getElementById('itemTemplate');
        let itemIndex = 0;
        const MAX_ITEMS = 50; // Maksimal 50 item untuk menghindari error
        
        // Peta untuk menyimpan data produk (stok, foto)
        const productDataMap = new Map();

        // ---- Fungsi bantuan ----
        function formatRupiah(angka) {
            return 'Rp ' + Number(angka).toLocaleString('id-ID');
        }

        function parseRupiah(str) {
            return parseInt((str || '0').replace(/[^0-9]/g, ''), 10) || 0;
        }

        function updateItemCount() {
            const count = container.querySelectorAll('.item-row').length;
            document.querySelector('#itemCount span').textContent = count;
            
            // Disable tombol tambah jika sudah mencapai limit
            const btnAdd = document.getElementById('btnAddItem');
            if (count >= MAX_ITEMS) {
                btnAdd.disabled = true;
                btnAdd.title = `Maksimal ${MAX_ITEMS} item produk`;
            } else {
                btnAdd.disabled = false;
                btnAdd.removeAttribute('title');
            }
        }

        function hitungSubtotal(row) {
            const jumlah = parseInt(row.querySelector('.jumlah')?.value || 0, 10);
            const harga = parseRupiah(row.querySelector('.harga-satuan')?.value || '0');
            const sub = jumlah * harga;
            const subtotalInput = row.querySelector('.subtotal');
            if (subtotalInput) subtotalInput.value = formatRupiah(sub);
            return sub;
        }

        function hitungTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                total += hitungSubtotal(row);
            });
            document.getElementById('totalKeseluruhan').innerText = formatRupiah(total);
        }

        // ---- Inisialisasi Select2 dengan Ajax ----
        function initSelect2(element, row) {
            const stokInfoDiv = row.querySelector('.stok-info');
            const stokBadge = row.querySelector('.stok-badge');
            const fotoPreview = row.querySelector('.foto-preview');
            const fotoImg = row.querySelector('.foto-img');

            $(element).select2({
                placeholder: "-- Cari Produk (SKU - Motif - Warna) --",
                allowClear: true,
                width: '100%',
                dropdownCssClass: 'produk-dropdown',
                templateResult: function (option) {
                    if (!option.id) return option.text;
                    return option.text;
                },
                templateSelection: function (option) {
                    if (!option.id) return option.text;
                    return option.text;
                },
                ajax: {
                    url: '<?= base_url("api/search/produk") ?>',
                    dataType: 'json',
                    delay: 300,
                    type: 'GET',
                    data: function (params) {
                        return { keyword: params.term };
                    },
                    processResults: function (data) {
                        data.forEach(function (item) {
                            productDataMap.set(parseInt(item.id), {
                                stok: item.stok,
                                foto: item.foto,
                                text: item.text
                            });
                        });
                        return {
                            results: data.map(function (item) {
                                return { id: parseInt(item.id), text: item.text };
                            })
                        };
                    }
                },
                minimumInputLength: 1
            });

            // Tampilkan stok dan foto saat produk dipilih
            $(element).on('change', function () {
                const selectedId = parseInt($(this).val());
                const produkData = productDataMap.get(selectedId);
                
                if (produkData) {
                    // Tampilkan stok
                    if (stokBadge) {
                        stokBadge.textContent = produkData.stok;
                    }
                    stokInfoDiv.style.display = 'block';
                    
                    // Tampilkan foto
                    if (produkData.foto && fotoImg) {
                        $(fotoImg).attr('src', produkData.foto);
                        $(fotoPreview).css('display', 'block');
                    }
                } else {
                    stokInfoDiv.style.display = 'none';
                    $(fotoPreview).hide();
                }
            });
        }

        // ---- Tambah baris item ----
        function addItem() {
            if (container.querySelectorAll('.item-row').length >= MAX_ITEMS) {
                alert(`Maksimal ${MAX_ITEMS} item produk per transaksi`);
                return;
            }

            const clone = template.content.cloneNode(true);
            const row = clone.querySelector('.item-row');
            row.innerHTML = row.innerHTML.replace(/INDEX/g, itemIndex);

            const select = row.querySelector('.produk-select');
            const fotoImg = row.querySelector('.foto-img');
            const jumlahInput = row.querySelector('.jumlah');
            const hargaInput = row.querySelector('.harga-satuan');

            container.appendChild(row);

            // Inisialisasi Select2
            initSelect2(select, row);

            // Klik gambar untuk zoom
            $(fotoImg).on('click', function () {
                const src = $(this).attr('src');
                if (src && !src.includes('no-image')) {
                    $('#zoomImage').attr('src', src);
                    $('#zoomModal').modal('show');
                }
            });

            // Hitung ulang total jika jumlah atau harga berubah
            jumlahInput.addEventListener('input', function () {
                // Validasi max jumlah
                const selectedId = parseInt(select.value);
                const produkData = productDataMap.get(selectedId);
                if (produkData) {
                    const jumlah = parseInt(this.value || 0, 10);
                    if (jumlah > produkData.stok) {
                        this.value = produkData.stok;
                        alert(`Stok hanya tersedia: ${produkData.stok}`);
                    }
                }
                hitungTotal();
            });

            hargaInput.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
                hitungTotal();
            });

            // Tombol hapus baris
            row.querySelector('.btn-remove-item').addEventListener('click', function () {
                $(select).select2('destroy');
                row.remove();
                updateItemCount();
                hitungTotal();
            });

            itemIndex++;
            updateItemCount();
            hitungTotal();
        }

        // ---- Event tombol tambah ----
        document.getElementById('btnAddItem').addEventListener('click', addItem);

        // ===== RESTORE OLD ITEMS SETELAH VALIDASI GAGAL =====
        <?php $oldItems = old('items'); ?>
        <?php if (!empty($oldItems)): ?>
            const oldItems = <?= json_encode($oldItems) ?>;
            const itemsArray = Array.isArray(oldItems) ? oldItems : Object.values(oldItems);
            
            itemsArray.forEach(function(item) {
                addItem();
                const rows = container.querySelectorAll('.item-row');
                const lastRow = rows[rows.length - 1];
                const sel = lastRow.querySelector('.produk-select');
                const jumlahInput = lastRow.querySelector('.jumlah');
                const hargaInput = lastRow.querySelector('.harga-satuan');
                
                // Set jumlah dan harga
                if (jumlahInput && item.jumlah) jumlahInput.value = item.jumlah;
                if (hargaInput && item.harga_satuan) hargaInput.value = item.harga_satuan;
                
                // Set produk via Select2 (create option + trigger)
                if (sel && item.id_produk) {
                    // Fetch product info to populate Select2
                    fetch('<?= base_url("api/search/produk") ?>?keyword=' + item.id_produk)
                        .then(r => r.json())
                        .then(data => {
                            const found = data.find(p => String(p.id) === String(item.id_produk));
                            if (found) {
                                productDataMap.set(parseInt(found.id), {
                                    stok: found.stok,
                                    foto: found.foto,
                                    text: found.text
                                });
                                const option = new Option(found.text, found.id, true, true);
                                $(sel).append(option).trigger('change');
                            }
                        });
                }
                
                // Recalculate
                hitungTotal();
            });
        <?php else: ?>
            // Satu baris awal default
            if (container.children.length === 0) {
                addItem();
            } else {
                updateItemCount();
            }
        <?php endif; ?>

        // ---- Sinkronisasi pelanggan -> nama pembeli ----
        const pelangganSelect = document.getElementById('pelanggan');
        const namaPembeliInput = document.getElementById('nama_pembeli');

        function syncNamaPembeli() {
            const selectedOption = pelangganSelect.options[pelangganSelect.selectedIndex];
            const namaPelanggan = selectedOption.getAttribute('data-nama');
            if (namaPelanggan) {
                namaPembeliInput.value = namaPelanggan;
                namaPembeliInput.setAttribute('readonly', true);
            } else {
                namaPembeliInput.value = '';
                namaPembeliInput.removeAttribute('readonly');
            }
        }

        pelangganSelect.addEventListener('change', syncNamaPembeli);
        syncNamaPembeli(); // inisialisasi

        // Validasi form sebelum submit
        document.querySelector('form').addEventListener('submit', function (e) {
            const items = container.querySelectorAll('.item-row');
            if (items.length === 0) {
                e.preventDefault();
                alert('Minimal satu item produk harus ditambahkan');
                return false;
            }

            // Cek setiap item
            let hasError = false;
            items.forEach((row, index) => {
                const select = row.querySelector('.produk-select');
                const jumlah = parseInt(row.querySelector('.jumlah')?.value || 0, 10);
                const harga = parseRupiah(row.querySelector('.harga-satuan')?.value || '0');

                if (!select.value) {
                    alert(`Item ${index + 1}: Produk belum dipilih`);
                    hasError = true;
                    return;
                }
                if (jumlah <= 0) {
                    alert(`Item ${index + 1}: Jumlah harus lebih dari 0`);
                    hasError = true;
                    return;
                }
                if (harga <= 0) {
                    alert(`Item ${index + 1}: Harga harus lebih dari 0`);
                    hasError = true;
                    return;
                }
            });

            if (hasError) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>

<style>
    /* Styling untuk item row */
    .item-row {
        background-color: #f8f9fc;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        border: 1px solid #e0e4e8;
        transition: all 0.3s ease;
    }

    .item-row:hover {
        background-color: #f0f3f7;
        border-color: #d0d8e0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Styling untuk foto preview */
    .foto-preview {
        margin-top: 8px;
        display: none;
        align-items: center;
        gap: 8px;
        padding: 8px;
        background-color: #fff;
        border-radius: 6px;
        border: 1px solid #ddd;
    }

    .foto-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .foto-img:hover {
        transform: scale(1.05);
        border-color: #007bff;
    }

    /* Styling untuk info stok */
    .stok-info {
        border-left: 4px solid #28a745 !important;
        background-color: #f0fdf4 !important;
        border-color: #28a745 !important;
    }

    .stok-badge {
        font-size: 1.1em;
        padding: 0.4em 0.8em !important;
    }

    /* Responsif untuk Select2 */
    .select2-container {
        width: 100% !important;
    }

    .select2-container--open .select2-dropdown {
        z-index: 1050;
    }

    /* Dropdown styling */
    .produk-dropdown {
        min-width: 300px !important;
    }

    .select2-results__option {
        padding: 10px 16px;
        white-space: normal;
        word-wrap: break-word;
    }

    /* Label dengan required indicator */
    .form-label .text-danger {
        margin-left: 2px;
    }

    /* Button styling */
    #btnAddItem:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-remove-item {
        min-width: 36px;
        padding: 0.375rem 0.5rem;
    }

    /* Mobile responsif */
    @media (max-width: 768px) {
        .item-row {
            padding: 12px;
        }

        .foto-preview {
            flex-direction: column;
            align-items: flex-start;
        }

        .stok-info {
            margin-top: 8px !important;
            margin-bottom: 0 !important;
        }
    }

    /* Styling untuk input Rupiah */
    .harga-satuan {
        text-align: right;
    }

    .subtotal {
        text-align: right;
        background-color: #f5f5f5;
    }
</style>
<?= $this->endSection() ?>