<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('ID barang tidak ditemukan!'); window.location='barang.php';</script>";
    exit();
}

$data = $conn->query("SELECT * FROM barang WHERE id_barang = $id")->fetch_assoc();
$kategori = $conn->query("SELECT * FROM kategori");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = $_POST['nama'];
    $kategoriId = $_POST['kategori'];
    $hargaBeli  = $_POST['harga_beli'];
    $hargaJual  = $_POST['harga_jual'];
    $stok       = $_POST['stok'];

    $gambar     = $_FILES['gambar']['name'];
    $tmp        = $_FILES['gambar']['tmp_name'];

    if (!empty($gambar)) {
        $namaBaru = time() . '-' . $gambar;
        move_uploaded_file($tmp, "uploads/" . $namaBaru);
    } else {
        $namaBaru = $data['gambar'];
    }

    $stmt = $conn->prepare("UPDATE barang SET nama_barang=?, kategori_id=?, harga_beli=?, harga_jual=?, stok=?, gambar=? WHERE id_barang=?");
    $stmt->bind_param("siddisi", $nama, $kategoriId, $hargaBeli, $hargaJual, $stok, $namaBaru, $id);
    $stmt->execute();

    echo "<script>alert('Barang berhasil diubah!'); window.location='barang.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang | Kasir App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #EA7300;
            --primary-light: #ff9a3c;
            --primary-dark: #d66800;
            --text-light: #f8f9fa;
            --text-dark: #343a40;
            --border-radius: 12px;
            --card-shadow: 0 6px 16px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }
        
        body {
            background: #f5f7fa;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            transition: var(--transition);
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--text-light);
            padding-top: 30px;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1000;
            box-shadow: 5px 0 15px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-header h3 {
            font-weight: 700;
            margin-bottom: 0;
            font-size: 1.5rem;
        }
        
        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }
        
        .sidebar-brand i {
            margin-right: 10px;
        }
        
        .sidebar a {
            color: rgba(255,255,255,0.85);
            display: flex;
            align-items: center;
            padding: 14px 25px;
            text-decoration: none;
            transition: var(--transition);
            border-radius: 0 30px 30px 0;
            margin: 6px 0;
            font-weight: 500;
        }
        
        .sidebar a i {
            margin-right: 15px;
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            opacity: 0.85;
        }
        
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.15);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar a.active {
            background: white;
            color: var(--primary-color);
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .sidebar.hidden {
            transform: translateX(-100%);
        }
        
        .sidebar-footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
            padding: 0 20px;
            font-size: 0.85rem;
            text-align: center;
            color: rgba(255,255,255,0.6);
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
            transition: var(--transition);
        }
        
        .main-content.full {
            margin-left: 0;
        }
        
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 15px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
        }
        
        .page-title {
            font-weight: 700;
            font-size: 1.6rem;
            color: var(--text-dark);
            margin-bottom: 0;
            display: flex;
            align-items: center;
        }
        
        .page-title i {
            color: var(--primary-color);
            margin-right: 12px;
        }
        
        /* Form Styles */
        .form-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 30px;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .form-container h5 {
            margin-bottom: 20px;
            color: var(--text-dark);
            font-weight: 600;
        }
        
        .form-label {
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            transition: var(--transition);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(234, 115, 0, 0.15);
        }
        
        .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            height: auto;
            border: 1px solid #e2e8f0;
        }
        
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(234, 115, 0, 0.15);
        }
        
        /* Button Styles */
        .btn {
            border-radius: 10px;
            padding: 10px 24px;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 15px rgba(234, 115, 0, 0.3);
        }
        
        .btn-primary i {
            margin-right: 8px;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(234, 115, 0, 0.3);
        }
        
        .btn-secondary {
            background-color: #e2e8f0;
            border: none;
            color: #4a5568;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .btn-secondary i {
            margin-right: 8px;
        }
        
        .btn-secondary:hover {
            background-color: #cbd5e0;
            color: #2d3748;
        }
        
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        
        /* Toggle Button */
        .toggle-btn {
            background: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: var(--primary-color);
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            cursor: pointer;
            transition: var(--transition);
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1050;
            display: none;
        }
        
        .toggle-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
            backdrop-filter: blur(3px);
        }

        .overlay.show {
            display: block;
        }
        
        /* Image Preview */
        .img-preview-wrapper {
            margin-top: 15px;
        }
        
        .img-preview {
            max-width: 120px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border: 2px solid #f0f0f0;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            .toggle-btn {
                display: flex;
            }
            .main-content.with-sidebar {
                padding-top: 80px;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<!-- Toggle Button (mobile) -->
<button class="toggle-btn" id="toggle-btn" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<!-- Overlay -->
<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <i class="fas fa-cash-register fa-2x"></i>
            <h3>Kasir App</h3>
        </div>
    </div>
    
    <a href="dashboard.php">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="kategori.php">
        <i class="fas fa-tags"></i> Kategori
    </a>
    <a href="barang.php" class="active">
        <i class="fas fa-box"></i> Barang
    </a>
    <a href="penjualan.php">
        <i class="fas fa-shopping-cart"></i> Penjualan
    </a>
    <a href="laporan.php">
        <i class="fas fa-chart-bar"></i> Laporan
    </a>
    <a href="logout.php">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
    
    <div class="sidebar-footer">
        &copy; <?= date('Y') ?> Kasir App
    </div>
</div>

<!-- Main Content -->
<div class="main-content" id="main-content">
    <div class="top-bar">
        <h4 class="page-title"><i class="fas fa-edit"></i> Edit Barang</h4>
        
        <div class="d-flex align-items-center gap-3">
            <a href="barang.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama_barang']) ?>" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <?php while ($row = $kategori->fetch_assoc()): ?>
                                <option value="<?= $row['id_kategori'] ?>" <?= $data['kategori_id'] == $row['id_kategori'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['nama_kategori']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Harga Beli (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            <input type="number" name="harga_beli" value="<?= $data['harga_beli'] ?>" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Harga Jual (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                            <input type="number" name="harga_jual" value="<?= $data['harga_jual'] ?>" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-cubes"></i></span>
                            <input type="number" name="stok" value="<?= $data['stok'] ?>" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Gambar Barang</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti gambar.</small>
                        
                        <?php if ($data['gambar']): ?>
                        <div class="img-preview-wrapper mt-3">
                            <img src="uploads/<?= $data['gambar'] ?>" class="img-preview" alt="Preview Barang">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="barang.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Toggle sidebar functionality
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        sidebar.classList.toggle('active');
        overlay.classList.toggle('show');
    }
    
    // Detect active page and highlight in sidebar
    document.addEventListener('DOMContentLoaded', function() {
        const currentLocation = window.location.href;
        const menuItems = document.querySelectorAll('.sidebar a');
        
        menuItems.forEach(item => {
            if(currentLocation.includes(item.getAttribute('href'))) {
                item.classList.add('active');
            }
        });
        
        // Preview image when a new file is selected
        const fileInput = document.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    let previewWrapper = document.querySelector('.img-preview-wrapper');
                    if (!previewWrapper) {
                        previewWrapper = document.createElement('div');
                        previewWrapper.className = 'img-preview-wrapper mt-3';
                        this.parentNode.appendChild(previewWrapper);
                    }
                    
                    let preview = previewWrapper.querySelector('.img-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.className = 'img-preview';
                        preview.alt = 'Preview Barang';
                        previewWrapper.appendChild(preview);
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
</script>

</body>
</html>