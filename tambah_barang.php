<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Ambil kategori
$kategori = $conn->query("SELECT * FROM kategori");

// Proses submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama       = $_POST['nama'];
    $kategoriId = $_POST['kategori'];
    $hargaBeli  = $_POST['harga_beli'];
    $hargaJual  = $_POST['harga_jual'];
    $stok       = $_POST['stok'];
    $gambar     = $_FILES['gambar']['name'];
    $tmp        = $_FILES['gambar']['tmp_name'];

    // Upload gambar
    if (!empty($gambar)) {
        $namaBaru = time() . '-' . $gambar;
        move_uploaded_file($tmp, "uploads/" . $namaBaru);
    } else {
        $namaBaru = null;
    }

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO barang (nama_barang, kategori_id, harga_beli, harga_jual, stok, gambar) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siddis", $nama, $kategoriId, $hargaBeli, $hargaJual, $stok, $namaBaru);
    $stmt->execute();

    echo "<script>alert('Barang berhasil ditambahkan!'); window.location='barang.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang</title>
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
    <h3>Tambah Barang Baru</h3>
    <form method="POST" enctype="multipart/form-data" class="mt-4">
        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Kategori</label>
            <select name="kategori" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                <?php while ($row = $kategori->fetch_assoc()): ?>
                    <option value="<?= $row['id_kategori'] ?>"><?= $row['nama_kategori'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Harga Beli</label>
            <input type="number" name="harga_beli" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Harga Jual</label>
            <input type="number" name="harga_jual" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Gambar Barang</label>
            <input type="file" name="gambar" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="barang.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

</body>
</html>
