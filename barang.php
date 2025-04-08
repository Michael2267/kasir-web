<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Ambil data barang
$sql = "SELECT b.*, k.nama_kategori 
        FROM barang b 
        LEFT JOIN kategori k ON b.kategori_id = k.id_kategori";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Barang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f8;
        }
        .sidebar {
            background-color: #0d6efd;
            color: white;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 220px;
            z-index: 1040;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 12px 20px;
            text-decoration: none;
            transition: background 0.2s, padding-left 0.2s;
        }
        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.1);
            padding-left: 30px;
        }
        .main-content {
            margin-left: 220px;
            padding: 20px;
        }
        .topbar {
            background-color: #ffffff;
            padding: 10px 20px;
            border-bottom: 1px solid #dee2e6;
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .img-thumbnail {
            max-width: 60px;
            height: auto;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                position: fixed;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .toggle-btn {
                display: block;
            }
        }

        .toggle-btn {
            display: none;
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 10px 15px;
            margin: 15px;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1050;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<!-- Toggle Button (Mobile) -->
<button class="toggle-btn" onclick="toggleSidebar()">☰</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
        <h4 class="text-center">Kasir App</h4>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="kategori.php">📁 Kategori</a>
        <a href="barang.php">📦 Barang</a>
        <a href="penjualan.php">🛒 Penjualan</a>
        <a href="laporan.php">📊 Laporan</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <!-- Topbar -->
    <div class="topbar">
        <h5 class="mb-0">📦 Daftar Barang</h5>
        <a href="tambah_barang.php" class="btn btn-sm btn-success">+ Tambah Barang</a>
    </div>

    <!-- Table -->
    <div class="table-responsive shadow-sm rounded bg-white p-3 mt-4">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Tanggal Input</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): $no = 1; ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                        <?php if (!empty($row['gambar']) && file_exists("gambar_barang/" . $row['gambar'])): ?>
    <img src="gambar_barang/<?= htmlspecialchars($row['gambar']) ?>" class="img-thumbnail" alt="Gambar Barang" width="70">
<?php else: ?>
    <span class="text-muted">-</span>
<?php endif; ?>

                        </td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
                        <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
                        <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
                        <td><?= $row['stok'] ?></td>
                        <td><?= $row['tanggal_input'] ?></td>
                        <td>
                            <a href="edit_barang.php?id=<?= $row['id_barang'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="hapus_barang.php?id=<?= $row['id_barang'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus barang ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center">Belum ada data barang.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }
</script>

</body>
</html>
