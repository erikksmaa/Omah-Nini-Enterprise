<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <!-- Keranjang Belanja -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-cart"></i> Keranjang Belanja</h5>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cartBody">
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Keranjang kosong</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">TOTAL</td>
                                    <td colspan="2" class="fw-bold" id="totalDisplay">Rp 0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Transaksi -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Form Transaksi</h5>
                </div>
                <div class="card-body p-3">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <p class="mb-0"><?= $error ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('kasir/penjualan/store') ?>" method="POST" id="formTransaksi">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label>No. Invoice</label>
                            <input type="text" class="form-control" value="<?= $no_invoice ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label>Cari Produk</label>
                            <div class="input-group">
                                <input type="text" id="searchProduk" class="form-control"
                                    placeholder="Ketik SKU atau nama produk...">
                                <button class="btn btn-primary" type="button" id="btnSearch">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <div id="searchResult" class="mt-2"
                                style="max-height: 200px; overflow-y: auto; display: none;">
                                <ul class="list-group" id="resultList"></ul>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Tipe Pembayaran <span class="text-danger">*</span></label>
                                    <select name="tipe_pembayaran" class="form-control" required>
                                        <option value="">Pilih</option>
                                        <option value="tunai">Tunai</option>
                                        <option value="transfer">Transfer</option>
                                        <option value="qris">QRIS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Total Belanja</label>
                                    <input type="text" id="totalBelanja" class="form-control" readonly>
                                    <input type="hidden" name="total_belanja" id="totalBelanjaHidden">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Bayar <span class="text-danger">*</span></label>
                                    <input type="number" name="bayar" id="bayar" class="form-control" value="0"
                                        step="1000" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Kembalian</label>
                                    <input type="text" id="kembalian" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2"
                                placeholder="Catatan transaksi..."></textarea>
                        </div>

                        <input type="hidden" name="items" id="itemsData">

                        <button type="submit" class="btn btn-success w-100" id="btnSubmit">
                            <i class="bi bi-check-lg"></i> Proses Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let total = 0;
    let searchTimeout;

    // Render keranjang
    function renderCart() {
        let tbody = document.getElementById('cartBody');
        let totalDisplay = document.getElementById('totalDisplay');

        if (cart.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Keranjang kosong</td></tr>';
            totalDisplay.innerHTML = 'Rp 0';
            document.getElementById('totalBelanja').value = 'Rp 0';
            document.getElementById('totalBelanjaHidden').value = 0;
            document.getElementById('itemsData').value = '';
            return;
        }

        let html = '';
        total = 0;

        cart.forEach((item, index) => {
            total += item.subtotal;
            html += `
                <tr>
                    <td><strong>${item.nama_produk}</strong><br><small class="text-muted">${item.sku}</small></td>
                    <td>Rp ${item.harga_jual.toLocaleString('id-ID')}</td>
                    <td>
                        <div class="input-group" style="width: 120px;">
                            <button class="btn btn-sm btn-outline-secondary" onclick="updateJumlah(${index}, -1)">-</button>
                            <input type="text" class="form-control form-control-sm text-center" value="${item.jumlah}" readonly>
                            <button class="btn btn-sm btn-outline-secondary" onclick="updateJumlah(${index}, 1)">+</button>
                        </div>
                    </td>
                    <td>Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" onclick="hapusItem(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        totalDisplay.innerHTML = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('totalBelanja').value = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('totalBelanjaHidden').value = total;
        document.getElementById('itemsData').value = JSON.stringify(cart);

        // Hitung kembalian
        hitungKembalian();
    }

    // Update jumlah
    function updateJumlah(index, perubahan) {
        let newJumlah = cart[index].jumlah + perubahan;
        if (newJumlah < 1) return;
        if (newJumlah > cart[index].stok) {
            alert('Stok tidak mencukupi! Stok tersedia: ' + cart[index].stok);
            return;
        }

        cart[index].jumlah = newJumlah;
        cart[index].subtotal = cart[index].harga_jual * newJumlah;
        renderCart();
    }

    // Hapus item
    function hapusItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function hitungKembalian() {
        let bayar = parseInt(document.getElementById('bayar').value) || 0;
        let total = <?= isset($total) ? $total : 0 ?>; // Atau ambil dari hidden field

        // Ambil total dari hidden field
        total = parseInt(document.getElementById('totalBelanjaHidden').value) || 0;

        let kembalian = bayar - total;
        let kembalianInput = document.getElementById('kembalian');
        let btnSubmit = document.getElementById('btnSubmit');

        if (kembalian < 0) {
            kembalianInput.value = 'Kurang Rp ' + Math.abs(kembalian).toLocaleString('id-ID');
            kembalianInput.classList.add('text-danger');
            btnSubmit.disabled = true;
            btnSubmit.textContent = 'Pembayaran Kurang';
        } else {
            kembalianInput.value = 'Rp ' + kembalian.toLocaleString('id-ID');
            kembalianInput.classList.remove('text-danger');
            btnSubmit.disabled = false;
            btnSubmit.textContent = 'Proses Pembayaran';
        }
    }

    // Search produk
    function searchProduk() {
        let keyword = document.getElementById('searchProduk').value;
        let resultDiv = document.getElementById('searchResult');
        let resultList = document.getElementById('resultList');

        if (keyword.length < 2) {
            resultDiv.style.display = 'none';
            return;
        }

        fetch(`<?= base_url('kasir/penjualan/search-produk') ?>?q=${encodeURIComponent(keyword)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    resultList.innerHTML = data.map(produk => `
                        <li class="list-group-item list-group-item-action" onclick="tambahProduk(${produk.id}, '${produk.nama_barang}', '${produk.sku}', ${produk.harga_jual}, ${produk.stok})">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>${produk.nama_barang}</strong>
                                    <br><small class="text-muted">${produk.sku} | Stok: ${produk.stok}</small>
                                </div>
                                <div class="text-end">
                                    <strong>Rp ${produk.harga_jual.toLocaleString('id-ID')}</strong>
                                </div>
                            </div>
                        </li>
                    `).join('');
                    resultDiv.style.display = 'block';
                } else {
                    resultList.innerHTML = '<li class="list-group-item text-center text-muted">Produk tidak ditemukan</li>';
                    resultDiv.style.display = 'block';
                }
            });
    }

    // Tambah produk ke keranjang
    function tambahProduk(id, nama, sku, harga, stok) {
        // Cek duplikat
        let existing = cart.find(item => item.id_produk == id);
        if (existing) {
            alert('Produk sudah ada di keranjang!');
            document.getElementById('searchResult').style.display = 'none';
            document.getElementById('searchProduk').value = '';
            return;
        }

        cart.push({
            id_produk: id,
            nama_produk: nama,
            sku: sku,
            harga_jual: harga,
            jumlah: 1,
            subtotal: harga,
            stok: stok
        });

        renderCart();
        document.getElementById('searchResult').style.display = 'none';
        document.getElementById('searchProduk').value = '';
    }

    // Event listeners
    document.getElementById('btnSearch').addEventListener('click', searchProduk);
    document.getElementById('searchProduk').addEventListener('keyup', function (e) {
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(searchProduk, 500);
        if (e.key === 'Enter') searchProduk();
    });
    document.getElementById('bayar').addEventListener('input', hitungKembalian);

    // Validasi submit
    document.getElementById('formTransaksi').addEventListener('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Keranjang belanja masih kosong!');
            return false;
        }

        let total = parseInt(document.getElementById('totalBelanjaHidden').value) || 0;
        let bayar = parseInt(document.getElementById('bayar').value) || 0;

        if (bayar < total) {
            e.preventDefault();
            alert('Pembayaran kurang dari total belanja!');
            return false;
        }

        let tipeBayar = document.querySelector('select[name="tipe_pembayaran"]').value;
        if (!tipeBayar) {
            e.preventDefault();
            alert('Pilih tipe pembayaran!');
            return false;
        }

        return true;
    });

    // Close search result when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#searchProduk') && !e.target.closest('#searchResult')) {
            document.getElementById('searchResult').style.display = 'none';
        }
    });
</script>

<style>
    #searchResult {
        position: absolute;
        z-index: 1000;
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        width: calc(100% - 30px);
    }

    .list-group-item-action {
        cursor: pointer;
    }

    .list-group-item-action:hover {
        background-color: #f8f9fa;
    }
</style>
<?= $this->endSection() ?>