<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['nama_user'])){
    header("location:index.php");
    exit();
}

// Ambil role, bersihkan spasi, dan paksa jadi huruf kecil semua
$role_raw = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$role = strtolower(trim($role_raw));

// Ambil data ringkasan penjualan hari ini khusus untuk tampilan
$total_trx_hari_ini = 0;
$total_omzet_hari_ini = 0;
try {
    // Format tanggal Access biasanya menggunakan Date() untuk hari ini
    $sql_summary = "SELECT COUNT(*) as jml, SUM(total_bayar) as total FROM penjualan_header WHERE tanggal = Date()";
    $query_summary = $conn->query($sql_summary);
    $res_summary = $query_summary->fetch();
    $total_trx_hari_ini = $res_summary['jml'] ?? 0;
    $total_omzet_hari_ini = $res_summary['total'] ?? 0;
} catch (Exception $e) {
    // Abaikan jika tabel belum ada atau error
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bakso Darwoto</title>
    <!-- Google Fonts & Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --success: #10b981;
            --danger: #f43f5e;
            --warning: #f59e0b;
            --dark: #0f172a;
            --slate: #64748b;
            --glass: rgba(255, 255, 255, 0.8);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { 
            background: #f8fafc; 
            color: var(--dark); 
            min-height: 100vh;
            background-image: 
                radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.05) 0px, transparent 50%);
        }

        nav {
            background: var(--glass);
            backdrop-filter: blur(12px);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand { 
            font-weight: 800; 
            font-size: 1.4rem; 
            color: var(--dark); 
            letter-spacing: -1px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        .brand span { color: var(--primary); }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-info {
            text-align: right;
            display: none;
        }
        @media (min-width: 768px) { .user-info { display: block; } }

        .user-info .name { display: block; font-weight: 700; font-size: 0.9rem; color: var(--dark); }
        .user-info .role-label { display: block; font-size: 0.7rem; color: var(--slate); text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px; }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .logout-link {
            display: none; 
            color: var(--danger);
            text-decoration: none;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #fff1f2;
            transition: all 0.2s;
            align-items: center;
            justify-content: center;
        }
        
        @media (min-width: 768px) {
            .logout-link { display: flex; }
            .logout-link:hover { 
                background: var(--danger); 
                color: white; 
                transform: rotate(90deg);
            }
        }

        .container { max-width: 1100px; margin: 0 auto; padding: 40px 20px; }

        .welcome-hero {
            background: white;
            padding: 40px;
            border-radius: 32px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.03);
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .welcome-hero::after {
            content: '🍜';
            position: absolute;
            right: -20px;
            bottom: -20px;
            font-size: 150px;
            opacity: 0.05;
            transform: rotate(-15deg);
        }

        .welcome-hero p { 
            color: var(--primary); 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 1.5px;
            font-size: 0.75rem;
            margin-bottom: 8px;
        }
        
        .welcome-hero h1 { 
            font-size: 2.2rem; 
            font-weight: 800; 
            color: var(--dark);
            letter-spacing: -1px;
            line-height: 1.2;
        }

        /* Grid Menu Utama */
        .menu-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 640px) { 
            .menu-grid { grid-template-columns: repeat(2, 1fr); } 
        }
        
        /* Layout khusus Kasir: 3 Kolom agar dashboard terisi penuh */
        <?php if($role == 'kasir'): ?>
        @media (min-width: 1024px) { 
            .menu-grid { 
                grid-template-columns: repeat(3, 1fr); 
                max-width: 1000px; 
                margin: 0 auto; 
            } 
        }
        <?php else: ?>
        @media (min-width: 1024px) { 
            .menu-grid { grid-template-columns: repeat(4, 1fr); } 
        }
        <?php endif; ?>

        .menu-card {
            background: white;
            padding: 30px 20px;
            border-radius: 28px;
            text-decoration: none;
            color: inherit;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            position: relative;
            height: 100%;
        }

        .menu-card:hover:not(.no-hover) {
            transform: translateY(-10px);
            border-color: var(--primary);
            box-shadow: 0 25px 50px -12px rgba(79, 70, 229, 0.15);
        }

        .icon-box {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 24px;
            transition: 0.3s;
        }

        .menu-card:hover .icon-box { transform: scale(1.1) rotate(5deg); }

        .c-produk { background: #eef2ff; color: var(--primary); }
        .c-jual { background: #ecfdf5; color: var(--success); }
        .c-beli { background: #fff1f2; color: var(--danger); }
        .c-laporan { background: #fffbeb; color: var(--warning); }
        .c-info { background: #f8fafc; color: var(--slate); border: 1px solid #e2e8f0; }
        .c-stats { background: #eef2ff; color: var(--primary); border: 1px solid #e0e7ff; }

        .menu-card h3 { 
            font-size: 1.15rem; 
            font-weight: 800; 
            margin-bottom: 8px; 
            color: var(--dark);
        }
        
        .menu-card p { 
            font-size: 0.85rem; 
            color: var(--slate); 
            line-height: 1.5;
            font-weight: 500;
        }

        .arrow-link {
            margin-top: 20px;
            font-size: 1.2rem;
            opacity: 0;
            transition: 0.3s;
            transform: translateX(-10px);
            color: var(--primary);
        }

        .menu-card:hover .arrow-link { opacity: 1; transform: translateX(0); }

        .mobile-logout {
            display: block;
            margin-top: 40px;
            text-align: center;
        }
        
        @media (min-width: 768px) { .mobile-logout { display: none; } }
        
        .btn-mobile-out {
            color: var(--danger);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 12px 24px;
            border-radius: 14px;
            border: 2px solid #fee2e2;
            display: inline-block;
            transition: 0.2s;
        }
        .btn-mobile-out:active { background: var(--danger); color: white; }

        /* Styling Jam Digital */
        #clock { font-weight: 800; color: var(--primary); font-family: monospace; font-size: 1.1rem; }
        .stat-val { font-size: 1.2rem; font-weight: 800; color: var(--dark); }
    </style>
</head>
<body>

    <nav>
        <a href="menu_utama.php" class="brand">WARUNG<span>DARWOTO</span></a>
        <div class="user-profile">
            <div class="user-info">
                <span class="name"><?php echo $_SESSION['nama_user']; ?></span>
                <span class="role-label"><?php echo $role_raw; ?></span>
            </div>
            <div class="user-avatar">
                <?php echo strtoupper(substr($_SESSION['nama_user'], 0, 1)); ?>
            </div>
            <a href="logout.php" class="logout-link" title="Keluar">
                <span class="material-icons-round">logout</span>
            </a>
        </div>
    </nav>

    <div class="container">
        <!-- Hero Section -->
        <div class="welcome-hero">
            <p>Selamat Datang Kembali, <?php echo $_SESSION['nama_user']; ?></p>
            <h1>Apa yang ingin Anda kerjakan hari ini?</h1>
        </div>

        <!-- Menu Utama -->
        <div class="menu-grid">
            <!-- Penjualan muncul untuk semua role -->
            <a href="penjualan.php" class="menu-card">
                <div class="icon-box c-jual">💸</div>
                <h3>Penjualan</h3>
                <p>Buka kasir untuk mencatat pesanan pelanggan hari ini.</p>
                <div class="arrow-link">→</div>
            </a>

            <!-- Jika role Kasir, tampilkan kartu info & statistik tambahan -->
            <?php if($role == 'kasir'): ?>
            <div class="menu-card no-hover c-stats">
                <div class="icon-box" style="background: white;">📊</div>
                <h3>Ringkasan Hari Ini</h3>
                <p>
                    Total Transaksi:<br>
                    <span class="stat-val"><?php echo $total_trx_hari_ini; ?> Nota</span>
                </p>
                <p style="margin-top: 10px;">
                    Estimasi Omzet:<br>
                    <span class="stat-val">Rp <?php echo number_format($total_omzet_hari_ini, 0, ',', '.'); ?></span>
                </p>
            </div>

            <div class="menu-card no-hover c-info">
                <div class="icon-box" style="background: white;">🕒</div>
                <h3>Status Sesi</h3>
                <p>
                    Jam Aktif: <span id="clock">00:00:00</span><br>
                    Tanggal: <b><?php echo date('d F Y'); ?></b>
                </p>
                <p style="margin-top: 15px; font-style: italic; color: var(--primary); font-size: 0.8rem;">
                    "Tetap semangat melayani pelanggan dengan senyuman!"
                </p>
            </div>
            <?php endif; ?>

            <!-- Menu Khusus Admin (Disembunyikan jika role = kasir) -->
            <?php if($role != 'kasir'): ?>
            <a href="produk.php" class="menu-card">
                <div class="icon-box c-produk">📦</div>
                <h3>Produk</h3>
                <p>Kelola daftar menu makanan, minuman, dan update harga.</p>
                <div class="arrow-link">→</div>
            </a>

            <a href="pengeluaran.php" class="menu-card">
                <div class="icon-box c-beli">🛒</div>
                <h3>Belanja</h3>
                <p>Catat nota belanja bahan baku ke pasar secara detail.</p>
                <div class="arrow-link">→</div>
            </a>

            <a href="laporan.php" class="menu-card">
                <div class="icon-box c-laporan">📈</div>
                <h3>Laporan</h3>
                <p>Analisa laba rugi dan performa penjualan warung.</p>
                <div class="arrow-link">→</div>
            </a>
            <?php endif; ?>
        </div>

        <div class="mobile-logout">
            <a href="logout.php" class="btn-mobile-out">Keluar Aplikasi</a>
        </div>
    </div>

    <script>
        // Script Jam Digital
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds}`;
            
            const clockElement = document.getElementById('clock');
            if (clockElement) {
                clockElement.textContent = timeString;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

</body>
</html>