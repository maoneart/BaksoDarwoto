<?php
include 'koneksi.php';

if ($_POST) {
    $id_penjualan = $_POST['id_penjualan'];
    $tanggal      = $_POST['tanggal'];
    $total_bayar  = $_POST['total_bayar'];
    $metode       = $_POST['metode_bayar'];

    try {
        $conn->beginTransaction();

        // 1. Simpan ke Header
        $sql1 = "INSERT INTO penjualan_header (id_penjualan, tanggal, total_bayar, metode_bayar) VALUES (?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([$id_penjualan, $tanggal, $total_bayar, $metode]);

        // 2. Persiapan Simpan ke Detail (Banyak barang)
        $id_produk = $_POST['id_produk'];
        $jumlah    = $_POST['jumlah'];
        $subtotal  = $_POST['subtotal'];

        // Ambil semua id_detail yang sudah ada di database untuk mengecek gap
        $stmtCheck = $conn->query("SELECT id_detail FROM penjualan_detail");
        $existingIds = $stmtCheck->fetchAll(PDO::FETCH_COLUMN);
        
        // Ubah ke integer dan urutkan agar mudah mencari celah
        $existingIdsInt = array_map('intval', $existingIds);
        sort($existingIdsInt);

        // SQL Detail dengan 5 kolom (Termasuk id_detail manual)
        $sql2 = "INSERT INTO penjualan_detail (id_detail, id_penjualan, id_produk, jumlah, subtotal) VALUES (?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);

        for ($i = 0; $i < count($id_produk); $i++) {
            // --- LOGIKA MENCARI ID_DETAIL TERKECIL YANG KOSONG ---
            $next_id_candidate = 1;
            foreach ($existingIdsInt as $id) {
                if ($id == $next_id_candidate) {
                    $next_id_candidate++;
                } else if ($id > $next_id_candidate) {
                    // Ditemukan celah! Berhenti mencari.
                    break;
                }
            }
            
            // Format angka menjadi 3 digit (misal: 1 jadi 001)
            $formatted_id_detail = str_pad($next_id_candidate, 3, '0', STR_PAD_LEFT);

            // Eksekusi Simpan
            $stmt2->execute([$formatted_id_detail, $id_penjualan, $id_produk[$i], $jumlah[$i], $subtotal[$i]]);
            
            // Tambahkan ID yang baru dipakai ke daftar tracking agar loop berikutnya tidak pakai ID yang sama
            $existingIdsInt[] = $next_id_candidate;
            sort($existingIdsInt);
        }

        $conn->commit();
        // Mengubah redirect ke penjualan.php agar tetap di halaman kasir
        echo "<script>alert('Transaksi Berhasil!'); window.location='penjualan.php';</script>";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Gagal menyimpan transaksi: " . $e->getMessage();
    }
}
?>