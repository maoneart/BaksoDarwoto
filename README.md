🏪 Warung Darwoto - Sistem Informasi Manajemen<br>
Selamat datang di repositori Warung Darwoto. Proyek ini merupakan aplikasi berbasis web yang menggunakan PHP dengan integrasi database Microsoft Access melalui koneksi ODBC.

🛠️ Bahan & Prasyarat <br>
Gunakan bahan-bahan di bawah ini untuk memulai:

| No | Nama Bahan                                | Link Download                                                                 |
|----|-------------------------------------------|--------------------------------------------------------------------------------|
| 1  | XAMPP                                     | [Download](https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.12/xampp-windows-x64-8.2.12-0-VS16-installer.exe/download) |
| 2  | Microsoft Access Database Engine 2016     | [Download](https://www.microsoft.com/en-us/download/details.aspx?id=54920)     |


# 🚀 Langkah Instalasi

## 1️⃣ Instalasi Software
1. Jalankan installer **XAMPP** dan selesaikan prosesnya.  
2. Jalankan installer **Microsoft Access Database Engine 2016**.  
3. Unduh atau clone semua file dari repositori ini.  

---

## 2️⃣ Konfigurasi PHP (Aktivasi ODBC)
1. Buka **XAMPP Control Panel**.  
2. Klik tombol **Config** pada baris Apache → pilih `php.ini`.  
3. Cari baris: ;extension=pdo_odbc
4. Hapus tanda titik koma `;` di awal baris sehingga menjadi: extension=pdo_odbc
5. Simpan (`Save`) file tersebut.  

---

## 3️⃣ Persiapan Folder Project
1. Copy seluruh file project ini.  
2. Masuk ke folder instalasi XAMPP kamu (biasanya di `C:\xampp\htdocs\`).  
3. Buat folder baru bernama **warung_darwoto** dan paste file tadi di dalamnya.  

---

## ⚙️ Pengaturan ODBC Data Source (64-bit)
Langkah ini wajib dilakukan agar database `.accdb` terbaca:

1. Buka **Start Menu** → ketik: `ODBC Data Source (64-bit)`.  
2. Klik tab **System DSN** → klik tombol **Add...**.  
3. Pilih **Microsoft Access Driver** (*.mdb, *.accdb) → klik **Finish**.  
4. Isi data sebagai berikut:  
- **Data Source Name**: `db_darwoto`  
- **Description**: Database Aplikasi Warung  
5. Klik **Select...** → cari file `db_darwoto.accdb` di folder `htdocs/warung_darwoto`.  
6. Klik **OK** pada semua jendela yang terbuka.  

---

## 🖥️ Cara Menjalankan
1. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.  
2. Buka browser dan akses: http://localhost/warung_darwoto/index.php


---

## 👨‍💻 Kontributor
- **Nama Kamu / Darwoto** — Lead Developer  

Proyek ini dikembangkan untuk mendukung **digitalisasi UMKM**.  
