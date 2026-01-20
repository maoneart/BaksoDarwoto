<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $conn->beginTransaction();

        // 1. Hapus detail terlebih dahulu karena ada relasi (foreign key)
        $sql1 = "DELETE FROM pengeluaran_detail WHERE id_pengeluaran = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([$id]);

        // 2. Hapus header
        $sql2 = "DELETE FROM pengeluaran_header WHERE id_pengeluaran = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute([$id]);

        $conn->commit();
        echo "<script>alert('Data Berhasil Dihapus!'); window.location='pengeluaran.php';</script>";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Gagal menghapus data: " . $e->getMessage() . "'); window.location='pengeluaran.php';</script>";
    }
} else {
    header("location:pengeluaran.php");
}
?>