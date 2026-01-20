<?php
session_start();
include 'koneksi.php'; // Mengambil koneksi ke Access

$user = $_POST['username'];
$pass = $_POST['password'];

try {
    // Mencari di tabel Users (sesuaikan besar kecil huruf nama kolomnya)
    $query = $conn->prepare("SELECT * FROM Users WHERE username = ? AND password = ?");
    $query->execute([$user, $pass]);
    $data = $query->fetch();

    if ($data) {
        // Jika login sukses
        $_SESSION['nama_user'] = $data['nama_lengkap'];
        $_SESSION['role'] = $data['role'];
        
        header("location:menu_utama.php");
    } else {
        // Jika login gagal (username/password salah)
        header("location:index.php?pesan=gagal");
    }
} catch (PDOException $e) {
    echo "Masalah Database: " . $e->getMessage();
}
?>