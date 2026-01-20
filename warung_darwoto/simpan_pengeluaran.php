<?php
include 'koneksi.php';

if ($_POST) {
    $id_pengeluaran    = $_POST['id_pengeluaran'];
    $tanggal           = $_POST['tanggal'];
    $keterangan        = $_POST['keterangan']; // Tangkap data keterangan
    $total_pengeluaran = $_POST['total_pengeluaran'];

    try {
        $conn->beginTransaction();

        // 1. Simpan Header (Ditambah kolom keterangan)
        $sql1 = "INSERT INTO pengeluaran_header (id_pengeluaran, tanggal, keterangan, total_pengeluaran) VALUES (?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([$id_pengeluaran, $tanggal, $keterangan, $total_pengeluaran]);

        // 2. Simpan Detail (Banyak barang)
        $nama_barang   = $_POST['nama_barang'];
        $jumlah        = $_POST['jumlah'];
        $satuan        = $_POST['satuan'];
        $harga_satuan  = $_POST['harga_satuan'];
        $subtotal      = $_POST['subtotal'];

        // Ambil semua id_detail yang sudah ada di database untuk mengecek gap
        $stmtCheck = $conn->query("SELECT id_detail FROM pengeluaran_detail");
        $existingIds = $stmtCheck->fetchAll(PDO::FETCH_COLUMN);
        
        // Ubah ke integer dan urutkan agar mudah mencari celah
        $existingIdsInt = array_map('intval', $existingIds);
        sort($existingIdsInt);

        $sql2 = "INSERT INTO pengeluaran_detail (id_detail, id_pengeluaran, nama_barang, jumlah, satuan, harga_satuan, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);

        for ($i = 0; $i < count($nama_barang); $i++) {
            // Logika Cari Celah (Gap Filling)
            $next_id_detail = 1;
            foreach ($existingIdsInt as $id) {
                if ($id == $next_id_detail) {
                    $next_id_detail++;
                } else if ($id > $next_id_detail) {
                    break;
                }
            }
            
            $formatted_id_detail = str_pad($next_id_detail, 3, '0', STR_PAD_LEFT);

            $stmt2->execute([
                $formatted_id_detail, 
                $id_pengeluaran, 
                $nama_barang[$i], 
                $jumlah[$i], 
                $satuan[$i], 
                $harga_satuan[$i], 
                $subtotal[$i]
            ]);
            
            // Catat ID agar tidak bentrok di loop yang sama
            $existingIdsInt[] = $next_id_detail;
            sort($existingIdsInt);
        }

        $conn->commit();
        echo "<script>alert('Pengeluaran Berhasil Disimpan!'); window.location='pengeluaran.php';</script>";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Gagal menyimpan: " . $e->getMessage();
    }
}
?>