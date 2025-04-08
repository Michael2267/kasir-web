<?php
$host     = "localhost";
$user     = "root";         // ganti jika user database kamu berbeda
$password = "";             // isi jika ada password database
$database = "market";     // ganti dengan nama database kamu

$conn = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
