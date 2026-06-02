<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, width=device-width">
    <title>Struk Penjualan</title>
    <style>
        /* CSS untuk printer thermal 58mm */
        @page {
            size: 58mm auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 58mm;
            margin: 0;
            padding: 3mm;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
        }

        .header h3 {
            margin: 0;
            font-size: 14px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .divider-solid {
            border-top: 1px solid #000;
            margin: 5px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .items-table {
            width: 100%;
            margin: 5px 0;
        }

        .items-table th,
        .items-table td {
            text-align: left;
            padding: 2px 0;
        }

        .items-table td.qty,
        .items-table th.qty {
            text-align: center;
            width: 15%;
        }

        .items-table td.price,
        .items-table th.price {
            text-align: right;
            width: 30%;
        }

        .items-table td.subtotal,
        .items-table th.subtotal {
            text-align: right;
            width: 30%;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin: 5px 0;
        }

        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 10px;
        }

        .thankyou {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body onload="window.print(); setTimeout(function() { window.close(); }, 1000);">
    <!-- Header Struk -->
    <div class="header">
        <h3>SYARIFA BATIK</h3>
        <p>Jl. Gatot Subroto, Gg.2A, Banyurip</p>
        <p>Telp: 0856-4030-3333</p>
        <div class="divider"></div>
        <div class="row">
            <span>Tanggal: <?= date('d/m/Y') ?></span><br>
            <span>Invoice: <?= esc($header['no_invoice']) ?></span>
        </div>
        <div class="divider"></div>
    </div>

    <!-- Item Produk -->
    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th class="qty">Qty</th>
                <th class="price">Harga</th>
                <th class="subtotal">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <?php
                $subtotal = $item['jumlah'] * ($item['harga_satuan'] ?? 0);
                // Potong nama produk jika terlalu panjang
                $namaProduk = strlen($item['nama_produk']) > 25 ? $item['nama_produk'] : $item['nama_produk'];
                ?>
                <tr>
                    <td><?= " - ". esc($namaProduk) ?></td>
                    <td class="qty">x<?= $item['jumlah'] ?></td>
                    <td class="price"><?= number_format($item['harga_satuan'] ?? 0, 0, ',', '.') ?></td>
                    <td class="subtotal"><?= number_format($subtotal, 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="divider"></div>

    <!-- Total -->
    <div class="total-row">
        <span>TOTAL</span>
        <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
    </div>

    <?php if (!empty($header['catatan'])): ?>
        <div class="row">
            <span>Catatan: <?= esc($header['catatan']) ?></span>
        </div>
    <?php endif; ?>

    <div class="divider-solid"></div>

    <!-- Footer -->
    <div class="footer">
        <p>** Barang yang sudah dibeli tidak dapat dikembalikan **</p>
    </div>

    <div class="thankyou">
        Terima kasih!
    </div>
</body>

</html>