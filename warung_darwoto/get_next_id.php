<?php
include 'koneksi.php';

// Ambil tanggal dari input (Format: YYYY-MM-DD)
$tgl_pilih = $_GET['tgl']; 

$d = date('d', strtotime($tgl_pilih)); // Tanggal (21)
$m = date('m', strtotime($tgl_pilih)); // Bulan (01)
$y = date('y', strtotime($tgl_pilih)); // Tahun (26)

$prefix = "BD" . $d . $m . $y; // Hasil: BD210126

try {
    // Cari transaksi terakhir yang kodenya mirip BD210126...
    // Menggunakan TOP 1 dan ORDER BY DESC untuk mendapatkan nomor paling besar
    $q = $conn->query("SELECT TOP 1 id_penjualan FROM penjualan_header WHERE id_penjualan LIKE '$prefix%' ORDER BY id_penjualan DESC");
    $last = $q->fetch();

    if ($last) {
        // Ambil 3 angka terakhir, lalu tambah 1
        $no_urut = (int)substr($last['id_penjualan'], -3);
        $no_urut++;
    } else {
        // Jika hari itu belum ada transaksi sama sekali
        $no_urut = 1;
    }
    
    // Gabungkan prefix dengan no urut yang diformat jadi 3 digit (001)
    echo $prefix . str_pad($no_urut, 3, "0", STR_PAD_LEFT);

} catch (Exception $e) {
    echo $prefix . "001";
}
?>