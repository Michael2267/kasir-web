<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';
$filter = $_GET['filter'] ?? '';

if ($filter == 'today') {
    $tgl_awal = $tgl_akhir = date('Y-m-d');
} elseif ($filter == '7days') {
    $tgl_awal = date('Y-m-d', strtotime('-6 days'));
    $tgl_akhir = date('Y-m-d');
} elseif ($filter == '30days') {
    $tgl_awal = date('Y-m-d', strtotime('-29 days'));
    $tgl_akhir = date('Y-m-d');
}

$where = "";
if ($tgl_awal && $tgl_akhir) {
    $where = "WHERE DATE(p.tanggal_input) BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$total_query = "SELECT COUNT(*) as total FROM penjualan p JOIN barang b ON p.barang_id = b.id_barang $where";
$total_result = $conn->query($total_query);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

$query = "SELECT p.*, b.nama_barang 
          FROM penjualan p 
          JOIN barang b ON p.barang_id = b.id_barang 
          $where 
          ORDER BY p.tanggal_input DESC 
          LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
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
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 12px 20px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            margin-left: 220px;
            padding: 30px;
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
                transition: transform 0.3s ease-in-out;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .toggle-btn {
                display: block;
            }
            .main-content {
                margin-left: 0;
                padding-top: 70px;
            }
        }
    </style>
</head>
<body>

<button class="toggle-btn" onclick="toggleSidebar()">☰ Menu</button>

<div class="sidebar" id="sidebar">
    <h4 class="text-center mb-4">Kasir App</h4>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="kategori.php">📁 Kategori</a>
    <a href="barang.php">📦 Barang</a>
    <a href="penjualan.php">🛒 Penjualan</a>
    <a href="laporan.php">📊 Laporan</a>
    <a href="logout.php">🚪 Logout</a>
</div>

<div class="main-content">
    <div class="container bg-white p-4 rounded shadow-sm">
        <h3 class="mb-4">📊 Laporan Penjualan</h3>

        <form class="row g-3 mb-4" method="GET">
            <div class="col-md-3">
                <label>Tanggal Awal</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= htmlspecialchars($tgl_awal) ?>">
            </div>
            <div class="col-md-3">
                <label>Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= htmlspecialchars($tgl_akhir) ?>">
            </div>
            <div class="col-md-6 d-flex align-items-end gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">🔍 Tampilkan</button>
                <a href="laporan.php" class="btn btn-secondary">🔄 Reset</a>
                <a href="laporan.php?filter=today" class="btn btn-outline-info">📅 Hari Ini</a>
                <a href="laporan.php?filter=7days" class="btn btn-outline-info">🗓️ 7 Hari</a>
                <a href="laporan.php?filter=30days" class="btn btn-outline-info">🗓️ 30 Hari</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = $offset + 1;
                    $grand_total = 0;
                    while ($row = $result->fetch_assoc()):
                        $grand_total += $row['total'];
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= $row['jumlah'] ?></td>
                        <td>Rp<?= number_format($row['total'], 0, ',', '.') ?></td>
                        <td><?= $row['tanggal_input'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <tr class="table-warning fw-bold">
                        <td colspan="3" class="text-end">Grand Total</td>
                        <td colspan="2">Rp<?= number_format($grand_total, 0, ',', '.') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-3">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">« Prev</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next »</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }
</script>

</body>
</html>
