<?php
session_start();
$total = isset($_GET['total']) ? (int)$_GET['total'] : 0;
$bayar = isset($_GET['bayar']) ? (int)$_GET['bayar'] : 0;
$kembalian = $bayar - $total;

function rupiah($angka) {
    return 'Rp' . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }
        .struk-container {
            width: 320px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #0d6efd;
        }
        .info {
            text-align: center;
            font-size: 14px;
            margin-bottom: 16px;
            color: #555;
        }
        .line {
            border-bottom: 1px dashed #aaa;
            margin: 10px 0;
        }
        .item {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin: 4px 0;
        }
        .item-name {
            width: 60%;
        }
        .item-qty {
            width: 15%;
            text-align: right;
        }
        .item-total {
            width: 25%;
            text-align: right;
        }
        .total {
            font-weight: bold;
            font-size: 15px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #777;
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .struk-container {
                box-shadow: none;
                border-radius: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="struk-container">
        <h2>Kasir App</h2>
        <div class="info">
            <?= date('d M Y H:i:s') ?>
        </div>
        <div class="line"></div>

        <?php if (!empty($_SESSION['last_cart'])): ?>
            <?php foreach ($_SESSION['last_cart'] as $item): ?>
                <div class="item">
                    <div class="item-name"><?= htmlspecialchars($item['nama_barang']) ?></div>
                    <div class="item-qty"><?= $item['jumlah'] ?>x</div>
                    <div class="item-total"><?= rupiah($item['jumlah'] * $item['harga_jual']) ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="item">Tidak ada data belanja.</div>
        <?php endif; ?>

        <div class="line"></div>

        <div class="item total">
            <span>Total</span>
            <span><?= rupiah($total) ?></span>
        </div>
        <div class="item">
            <span>Bayar</span>
            <span><?= rupiah($bayar) ?></span>
        </div>
        <div class="item">
            <span>Kembalian</span>
            <span><?= rupiah($kembalian) ?></span>
        </div>

        <div class="line"></div>
        <div class="footer">
            Terima kasih!<br>
            Barang yang sudah dibeli tidak dapat dikembalikan.
        </div>
    </div>
</body>
</html>
