<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Ambil semua kategori
$kategori = $conn->query("SELECT * FROM kategori");

// Hapus kategori
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM kategori WHERE id_kategori = $id");
    echo "<script>alert('Kategori berhasil dihapus!'); window.location='kategori.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Kategori</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f8;
        }
        .sidebar {
            background: linear-gradient(to bottom, #0d6efd, #3c8dbc);
            color: white;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 220px;
            padding-top: 20px;
            transition: transform 0.3s ease-in-out;
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
            padding: 30px;
            transition: margin-left 0.3s ease-in-out;
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

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .toggle-btn {
                display: block;
            }
            .main-content {
                margin-left: 0 !important;
                padding-top: 70px;
            }
        }
    </style>
</head>
<body>

<!-- Toggle Button (Mobile) -->
<button class="toggle-btn" onclick="toggleSidebar()">☰ Menu</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h4 class="text-center mb-4">Kasir App</h4>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="kategori.php">📁 Kategori</a>
    <a href="barang.php">📦 Barang</a>
    <a href="penjualan.php">🛒 Penjualan</a>
    <a href="laporan.php">📊 Laporan</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <div class="container bg-white p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="m-0">Data Kategori</h3>
            <a href="tambah_kategori.php" class="btn btn-success">+ Tambah Kategori</a>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Kategori</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($kategori->num_rows > 0): $no = 1; ?>
                    <?php while ($row = $kategori->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                        <td>
                            <a href="kategori.php?hapus=<?= $row['id_kategori'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kategori ini?')">🗑 Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center text-muted">Belum ada kategori.</td></tr>
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
