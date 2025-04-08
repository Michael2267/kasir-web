<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$notif = "";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Tambahkan ke cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['barang_id'])) {
    $barang_id = $_POST['barang_id'];
    $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;

    $stmt = $conn->prepare("SELECT nama_barang, stok, harga_jual FROM barang WHERE id_barang = ?");
    $stmt->bind_param("i", $barang_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $barang = $result->fetch_assoc();

    if ($barang && $jumlah <= $barang['stok']) {
        if (isset($_SESSION['cart'][$barang_id])) {
            $_SESSION['cart'][$barang_id]['jumlah'] += $jumlah;
        } else {
            $_SESSION['cart'][$barang_id] = [
                'nama_barang' => $barang['nama_barang'],
                'harga_jual' => $barang['harga_jual'],
                'jumlah' => $jumlah
            ];
        }
    } else {
        $notif = "<div class='alert alert-danger'>Stok tidak cukup atau barang tidak ditemukan!</div>";
    }
}

// Proses bayar
if (isset($_POST['bayar'])) {
    $bayar = (int)$_POST['bayar'];
    $total_bayar = 0;
    foreach ($_SESSION['cart'] as $id => $item) {
        $total_bayar += $item['harga_jual'] * $item['jumlah'];
    }

    if ($bayar >= $total_bayar) {
        foreach ($_SESSION['cart'] as $id => $item) {
            $tanggal = date('Y-m-d');
            $stmt = $conn->prepare("INSERT INTO penjualan (barang_id, jumlah, total, tanggal_input) VALUES (?, ?, ?, ?)");
            $total = $item['harga_jual'] * $item['jumlah'];
            $stmt->bind_param("iids", $id, $item['jumlah'], $total, $tanggal);
            $stmt->execute();

            $stmtUpdate = $conn->prepare("UPDATE barang SET stok = stok - ? WHERE id_barang = ?");
            $stmtUpdate->bind_param("ii", $item['jumlah'], $id);
            $stmtUpdate->execute();
        }

        $kembalian = $bayar - $total_bayar;
        $_SESSION['last_cart'] = $_SESSION['cart'];
        $_SESSION['popup_data'] = [
            'total' => $total_bayar,
            'bayar' => $bayar,
            'kembalian' => $kembalian
        ];
        $_SESSION['cart'] = [];
        header("Location: penjualan.php?popup=1");
        exit();
    } else {
        $notif = "<div class='alert alert-danger'>Uang bayar kurang!</div>";
    }
}

$barangList = $conn->query("SELECT * FROM barang ORDER BY nama_barang ASC");
$barangData = [];
while ($row = $barangList->fetch_assoc()) {
    $barangData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .wrapper {
            display: flex;
            flex-direction: row;
            min-height: 100vh;
        }
        .sidebar {
            background: linear-gradient(to bottom, #0d6efd, #3c8dbc);
            color: white;
            padding: 20px 0;
            width: 220px;
            transition: all 0.3s;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar h4 {
            font-size: 1.25rem;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .main {
            flex: 1;
            padding: 20px;
        }
        .sidebar-toggler {
            position: absolute;
            top: 10px;
            right: -25px;
            background-color: #0d6efd;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            color: white;
            border: none;
        }
        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                position: relative;
            }
            .main {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="sidebar" id="sidebar">
        <button class="sidebar-toggler" onclick="toggleSidebar()">☰</button>
        <h4 class="text-center">Kasir</h4>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="kategori.php">📁 Kategori</a>
        <a href="barang.php">📦 Barang</a>
        <a href="penjualan.php">🛒 Penjualan</a>
        <a href="laporan.php">📊 Laporan</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="main">
        <h3 class="mb-4">Transaksi Penjualan</h3>
        <?= $notif ?>

        <form method="POST" id="formTambahBarang" class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="searchBarang">Cari Barang</label>
                <input type="text" id="searchBarang" class="form-control" placeholder="Ketik nama barang...">
                <input type="hidden" name="barang_id" id="barang_id">
            </div>
            <div class="col-md-3">
                <label for="jumlah">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" value="1" min="1">
            </div>
        </form>

        <h5>Keranjang</h5>
        <div class="table-responsive">
            <table class="table table-bordered bg-white">
                <thead class="table-secondary">
                    <tr>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_semua = 0;
                    foreach ($_SESSION['cart'] as $item): 
                        $total = $item['jumlah'] * $item['harga_jual'];
                        $total_semua += $total;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                            <td><?= $item['jumlah'] ?></td>
                            <td>Rp<?= number_format($item['harga_jual'], 0, ',', '.') ?></td>
                            <td>Rp<?= number_format($total, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <form method="POST" class="row g-3" id="formBayar">
            <div class="col-md-4">
                <label for="total">Total Bayar</label>
                <input type="text" class="form-control" value="Rp<?= number_format($total_semua, 0, ',', '.') ?>" readonly>
                <input type="hidden" id="totalBelanja" value="<?= $total_semua ?>">
            </div>
            <div class="col-md-4">
                <label for="bayar">Uang Bayar</label>
                <input type="number" name="bayar" id="bayar" class="form-control" required>
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary w-100">Bayar</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="kembalianModal" tabindex="-1" aria-labelledby="kembalianModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="kembalianModalLabel">Transaksi Berhasil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <p><strong>Total Belanja:</strong> Rp<span id="popupTotal"></span></p>
        <p><strong>Bayar:</strong> Rp<span id="popupBayar"></span></p>
        <p><strong>Kembalian:</strong> Rp<span id="popupKembalian"></span></p>
      </div>
      <div class="modal-footer">
        <a href="penjualan.php" class="btn btn-secondary">Transaksi Baru</a>
        <a href="#" id="cetakStrukBtn" class="btn btn-primary" target="_blank">Cetak Struk</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    }
</script>

<?php if (isset($_GET['popup']) && isset($_SESSION['popup_data'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const data = <?= json_encode($_SESSION['popup_data']) ?>;
        document.getElementById('popupTotal').innerText = data.total.toLocaleString('id-ID');
        document.getElementById('popupBayar').innerText = data.bayar.toLocaleString('id-ID');
        document.getElementById('popupKembalian').innerText = data.kembalian.toLocaleString('id-ID');
        document.getElementById("cetakStrukBtn").href = `struk.php?total=${data.total}&bayar=${data.bayar}&kembalian=${data.kembalian}`;

        const modal = new bootstrap.Modal(document.getElementById('kembalianModal'));
        modal.show();
    });
</script>
<?php unset($_SESSION['popup_data']); endif; ?>

<script>
    const barangList = <?= json_encode($barangData) ?>;
    $(function() {
        $("#searchBarang").autocomplete({
            source: barangList.map(b => ({
                label: `${b.nama_barang} (Stok: ${b.stok})`,
                value: b.nama_barang,
                id: b.id_barang
            })),
            select: function(event, ui) {
                $('#barang_id').val(ui.item.id);
                setTimeout(() => {
                    $('#formTambahBarang').submit();
                }, 100);
            },
            minLength: 1
        });
    });
</script>
</body>
</html>
