<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-cart-plus"></i> <?= $title ?></h5>
        </div>
        <div class="card-body p-3">
            <!-- Tampilkan Error -->
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <p class="mb-0"><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <form action="<?= base_url('gudang/pembelian/store') ?>" method="POST" id="formPembelian">
                <?= csrf_field() ?>
                
                <!-- Header Form -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>No. Invoice</label>
                            <input type="text" class="form-control" value="<?= $no_invoice ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_pembelian" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label>Supplier <span class="text-danger">*</span></label>
                            <select name="id_supplier" class="form-control">
                                <option value="">-- Pilih Supplier --</option>
                                <?php foreach ($supplier as $s): ?>
                                    <option value="<?= $s['id'] ?>"><?= $s['nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Tambah Item -->
                <div class="row mt-3">
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label>Produk <span class="text-danger">*</span></label>
                            <select id="produk_id" class="form-control">
                                <option value="">-- Pilih Produk --</option>
                                <?php foreach ($produk as $p): ?>
                                    <option value="<?= $p['id'] ?>" 
                                            data-harga="<?= $p['harga_beli'] ?>" 
                                            data-nama="<?= $p['nama_barang'] ?>">
                                        <?= $p['nama_barang'] ?> (<?= $p['sku'] ?>) - Rp <?= number_format($p['harga_beli'], 0, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label>Harga Beli</label>
                            <input type="number" id="harga_beli" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label>Jumlah</label>
                            <input type="number" id="jumlah" class="form-control" value="1" min="1">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label>&nbsp;</label>
                            <button type="button" id="btnTambah" class="btn btn-primary form-control">
                                <i class="bi bi-plus"></i> Tambah Produk
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabel Keranjang -->
                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Produk</th>
                                <th width="15%">Harga Beli</th>
                                <th width="10%">Jumlah</th>
                                <th width="15%">Subtotal</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada produk ditambahkan</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">TOTAL</td>
                                <td colspan="2" class="fw-bold" id="totalBayar">Rp 0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Catatan -->
                <div class="mb-3">
                    <label>Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                </div>

                <!-- Hidden Input untuk menyimpan items -->
                <input type="hidden" name="items" id="itemsData">

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-primary" id="btnSubmit">
                    <i class="bi bi-save"></i> Simpan Pembelian
                </button>
                <a href="<?= base_url('gudang/pembelian') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
</div>

<script>
    // Data keranjang
    let cart = [];

    // DOM Elements
    const produkSelect = document.getElementById('produk_id');
    const hargaBeliInput = document.getElementById('harga_beli');
    const jumlahInput = document.getElementById('jumlah');
    const btnTambah = document.getElementById('btnTambah');
    const cartBody = document.getElementById('cartBody');
    const totalBayarSpan = document.getElementById('totalBayar');
    const itemsDataInput = document.getElementById('itemsData');
    const form = document.getElementById('formPembelian');

    // Event: Pilih produk
    produkSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const harga = selectedOption.getAttribute('data-harga');
        hargaBeliInput.value = harga ? parseInt(harga) : '';
    });

    // Event: Tombol tambah
    btnTambah.addEventListener('click', function() {
        const produkId = produkSelect.value;
        const produkNama = produkSelect.options[produkSelect.selectedIndex]?.getAttribute('data-nama');
        const hargaBeli = parseInt(hargaBeliInput.value);
        const jumlah = parseInt(jumlahInput.value);

        // Validasi
        if (!produkId) {
            alert('Pilih produk terlebih dahulu');
            return;
        }

        if (!hargaBeli || hargaBeli <= 0) {
            alert('Harga beli tidak valid');
            return;
        }

        if (!jumlah || jumlah <= 0) {
            alert('Jumlah harus lebih dari 0');
            return;
        }

        // Cek duplikat
        const existingIndex = cart.findIndex(item => item.id_produk == produkId);
        if (existingIndex !== -1) {
            alert('Produk sudah ada di keranjang!');
            return;
        }

        // Hitung subtotal
        const subtotal = hargaBeli * jumlah;

        // Tambah ke cart
        cart.push({
            id_produk: produkId,
            nama_produk: produkNama,
            harga_beli: hargaBeli,
            jumlah: jumlah,
            subtotal: subtotal
        });

        // Update tabel
        renderCart();

        // Reset form
        produkSelect.value = '';
        hargaBeliInput.value = '';
        jumlahInput.value = '1';
    });

    // Render tabel keranjang
    function renderCart() {
        if (cart.length === 0) {
            cartBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Belum ada produk ditambahkan</td></tr>';
            totalBayarSpan.innerHTML = 'Rp 0';
            itemsDataInput.value = '';
            return;
        }

        let html = '';
        let total = 0;

        cart.forEach((item, index) => {
            total += item.subtotal;
            html += `
                <tr>
                    <td>${item.nama_produk}</td>
                    <td>Rp ${item.harga_beli.toLocaleString('id-ID')}</td>
                    <td>${item.jumlah}</td>
                    <td>Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(${index})">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
            `;
        });

        cartBody.innerHTML = html;
        totalBayarSpan.innerHTML = 'Rp ' + total.toLocaleString('id-ID');
        
        // Simpan ke hidden input
        itemsDataInput.value = JSON.stringify(cart);
    }

    // Hapus item dari keranjang
    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    // Validasi sebelum submit
    form.addEventListener('submit', function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Minimal tambahkan 1 produk!');
            return false;
        }

        if (itemsDataInput.value === '') {
            e.preventDefault();
            alert('Data keranjang kosong!');
            return false;
        }

        console.log('Data yang akan dikirim:', itemsDataInput.value);
        return true;
    });
</script>
<?= $this->endSection() ?>