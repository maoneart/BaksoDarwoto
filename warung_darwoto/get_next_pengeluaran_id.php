<?php
include 'koneksi.php';

// Pastikan ada parameter tanggal
if (!isset($_GET['tgl'])) {
    echo "ERR_TGL";
    exit();
}

$tgl = $_GET['tgl']; 
$dateObj = strtotime($tgl);

// Format: PBD + Tgl(2) + Bln(2) + Thn(2) + 001
// Contoh: PBD200126001
$prefix = "PBD" . date('dmy', $dateObj);

try {
    // Microsoft Access menggunakan SELECT TOP 1, bukan LIMIT 1
    $sql = "SELECT TOP 1 id_pengeluaran FROM pengeluaran_header WHERE id_pengeluaran LIKE ? ORDER BY id_pengeluaran DESC";
    $query = $conn->prepare($sql);
    $query->execute([$prefix . "%"]);
    $last = $query->fetch();

    if ($last) {
        // Mengambil 3 angka terakhir
        $lastNum = (int)substr($last['id_pengeluaran'], -3);
        $nextNum = $lastNum + 1;
    } else {
        $nextNum = 1;
    }

    // Kembalikan ID baru
    echo $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
} catch (PDOException $e) {
    // Jika error, tampilkan pesan agar mudah didebug
    echo "ERROR_DB: " . $e->getMessage();
}
?>