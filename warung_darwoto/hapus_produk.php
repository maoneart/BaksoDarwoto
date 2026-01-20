<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['nama_user'])){
    header("location:index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Cek apakah produk pernah digunakan di transaksi (opsional, tapi disarankan)
        // Jika tabel detail penjualan menggunakan FK ke produk, hapus akan gagal jika ada data.
        
        $sql = "DELETE FROM produk WHERE id_produk = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        echo "<script>alert('Produk Berhasil Dihapus!'); window.location='produk.php';</script>";
    } catch (PDOException $e) {
        // Jika gagal karena relasi database (sudah ada di transaksi)
        echo "<script>alert('Gagal! Produk ini tidak bisa dihapus karena sudah memiliki riwayat transaksi.'); window.location='produk.php';</script>";
    }
} else {
    header("location:produk.php");
}
?>