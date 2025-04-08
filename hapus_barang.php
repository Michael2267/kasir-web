<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Ambil nama gambar dulu
    $query = $conn->query("SELECT gambar FROM barang WHERE id_barang = $id");
    $data = $query->fetch_assoc();

    if ($data && !empty($data['gambar']) && file_exists("uploads/" . $data['gambar'])) {
        unlink("uploads/" . $data['gambar']); // hapus gambar
    }

    // Hapus dari DB
    $conn->query("DELETE FROM barang WHERE id_barang = $id");

    echo "<script>alert('Barang berhasil dihapus!'); window.location='barang.php';</script>";
} else {
    echo "<script>alert('ID barang tidak ditemukan!'); window.location='barang.php';</script>";
}
?>
