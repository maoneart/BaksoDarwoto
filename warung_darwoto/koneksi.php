<?php
$dsn = "db_darwoto"; // Nama yang Anda buat di ODBC 32-bit

try {
    $conn = new PDO("odbc:$dsn");
    // Jika berhasil, tidak muncul pesan apa-apa agar tampilan index rapi
} catch (PDOException $e) {
    echo "Koneksi ke Access Gagal: " . $e->getMessage();
    die();
}
?>