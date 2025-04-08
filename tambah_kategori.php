<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Proses submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama_kategori']);
    if (!empty($nama)) {
        $stmt = $conn->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
        $stmt->bind_param("s", $nama);
        $stmt->execute();

        echo "<script>alert('Kategori berhasil ditambahkan!'); window.location='kategori.php';</script>";
    } else {
        $error = "Nama kategori tidak boleh kosong.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f8;
        }
        .sidebar {
            height: 100vh;
            background: linear-gradient(to bottom, #0d6efd, #3c8dbc);
            color: white;
            padding-top: 20px;
            position: fixed;
            width: 220px;
            transition: width 0.3s;
        }
        .sidebar:hover {
            width: 240px;
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
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
<h4 class="text-center">Kasir App</h4>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="kategori.php">📁 Kategori</a>
        <a href="barang.php">📦 Barang</a>
        <a href="penjualan.php">🛒 Penjualan</a>
        <a href="laporan.php">📊 Laporan</a>
        <a href="logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    <h3>Tambah Kategori Baru</h3>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label>Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="kategori.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

</body>
</html>
