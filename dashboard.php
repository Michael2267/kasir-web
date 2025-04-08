<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

// Data Penjualan Hari Ini
$tanggal_hari_ini = date('Y-m-d');
$query_penjualan = $conn->query("SELECT SUM(total) as total_hari_ini FROM penjualan WHERE DATE(tanggal_input) = '$tanggal_hari_ini'");
$data_penjualan = $query_penjualan->fetch_assoc();
$total_hari_ini = $data_penjualan['total_hari_ini'] ?? 0;

// Jumlah Barang
$query_barang = $conn->query("SELECT COUNT(*) as jumlah_barang FROM barang");
$data_barang = $query_barang->fetch_assoc();
$jumlah_barang = $data_barang['jumlah_barang'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Kasir</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f1f3f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar {
            height: 100vh;
            background: linear-gradient(to bottom, #0d6efd, #3c8dbc);
            color: white;
            padding-top: 20px;
            position: fixed;
            left: 0;
            top: 0;
            width: 220px;
            transition: transform 0.4s ease, opacity 0.4s ease;
            transform: translateX(0);
            opacity: 1;
            z-index: 1000;
        }
        .sidebar a {
        color: white;
        display: block;
        padding: 12px 20px;
        text-decoration: none;
        transition: all 0.3s ease;
        }
        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.15);
            padding-left: 30px;
            border-left: 4px solid #fff;
        }
        .sidebar.hidden {
            transform: translateX(-100%);
            opacity: 0;
        }

        .main-content {
            margin-left: 220px;
            padding: 30px;
            transition: margin-left 0.3s;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
        }

        .card-modern {
            border: none;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            transition: 0.3s;
            background-color: #fff;
        }
        .card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.12);
        }
        .card-icon {
            font-size: 2.2rem;
            opacity: 0.7;
        }
        .card-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin-top: 10px;
        }
        .card-title {
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 1rem;
            color: #555;
        }

        .toggle-btn {
            font-size: 1.5rem;
            border: none;
            background: none;
            color: #0d6efd;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.3);
            z-index: 999;
            display: none;
        }

        .overlay.show {
            display: block;
        }
    </style>
</head>
<body>

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

    <!-- Overlay -->
    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Hamburger -->
        <button class="toggle-btn d-md-none mb-3" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>

        <h2 class="mb-4">👋 Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>

        <div class="container-fluid">
            <div class="row g-4">
                <!-- Card: Keuntungan Hari Ini -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card card-modern">
                        <div class="d-flex align-items-center">
                            <div class="text-primary me-3 card-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <div class="card-title">Keuntungan Hari Ini</div>
                                <div class="card-value text-primary">Rp<?= number_format($total_hari_ini, 0, ',', '.') ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Jumlah Barang -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card card-modern">
                        <div class="d-flex align-items-center">
                            <div class="text-success me-3 card-icon">
                                <i class="fas fa-boxes-stacked"></i>
                            </div>
                            <div>
                                <div class="card-title">Jumlah Barang</div>
                                <div class="card-value text-success"><?= $jumlah_barang ?> item</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tambahkan card lain di sini jika perlu -->
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    
    if (sidebar.classList.contains('active')) {
        sidebar.classList.remove('active');
        sidebar.classList.add('hidden');
        overlay.classList.remove('show');
    } else {
        sidebar.classList.remove('hidden');
        sidebar.classList.add('active');
        overlay.classList.add('show');
    }
}
    </script>

</body>
</html>
