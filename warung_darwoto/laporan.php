<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['nama_user'])){
    header("location:index.php");
    exit();
}

// Proteksi role: Hanya Admin yang bisa buka laporan (sesuai permintaan sebelumnya)
$role = isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : 'kasir';
if($role == 'kasir') {
    echo "<script>alert('Akses Ditolak! Hanya Admin yang dapat melihat laporan.'); window.location='menu_utama.php';</script>";
    exit();
}

// Default filter tanggal (Bulan ini)
$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');

try {
    // 1. Ambil Data Pemasukan (Penjualan)
    $stmt_jual = $conn->prepare("SELECT * FROM penjualan_header WHERE tanggal BETWEEN ? AND ? ORDER BY tanggal DESC");
    $stmt_jual->execute([$tgl_awal, $tgl_akhir]);
    $data_pemasukan = $stmt_jual->fetchAll();

    // 2. Ambil Data Pengeluaran (Belanja)
    $stmt_keluar = $conn->prepare("SELECT * FROM pengeluaran_header WHERE tanggal BETWEEN ? AND ? ORDER BY tanggal DESC");
    $stmt_keluar->execute([$tgl_awal, $tgl_akhir]);
    $data_pengeluaran = $stmt_keluar->fetchAll();

    // Hitung Total
    $total_pemasukan = 0;
    foreach($data_pemasukan as $j) { $total_pemasukan += $j['total_bayar']; }

    $total_pengeluaran = 0;
    foreach($data_pengeluaran as $p) { $total_pengeluaran += $p['total_pengeluaran']; }

    $laba_rugi = $total_pemasukan - $total_pengeluaran;

} catch (PDOException $e) {
    echo "Masalah Database: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Bakso Darwoto</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --success: #10b981;
            --danger: #f43f5e;
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
            padding-bottom: 50px;
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

        .brand { font-weight: 800; font-size: 1.2rem; color: var(--dark); letter-spacing: -1px; text-decoration: none; }
        .brand span { color: var(--primary); }

        .btn-back {
            text-decoration: none;
            color: var(--slate);
            font-size: 0.85rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: 0.2s;
        }
        .btn-back:hover { color: var(--primary); }

        .container { max-width: 1200px; margin: 0 auto; padding: 30px 20px; }

        /* Filter Section */
        .filter-card {
            background: white;
            padding: 25px;
            border-radius: 24px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
            margin-bottom: 30px;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .form-group { flex: 1; min-width: 200px; }
        label { display: block; font-size: 0.75rem; font-weight: 800; color: var(--slate); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        input { 
            width: 100%; padding: 12px 16px; border: 2px solid #f1f5f9; 
            border-radius: 14px; font-size: 0.95rem; outline: none; transition: 0.2s;
        }
        input:focus { border-color: var(--primary); }

        .btn-filter {
            background: var(--primary);
            color: white;
            padding: 12px 25px;
            border-radius: 14px;
            border: none;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }
        .btn-filter:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 28px;
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .stat-info p { font-size: 0.8rem; font-weight: 700; color: var(--slate); text-transform: uppercase; margin-bottom: 4px; }
        .stat-info h3 { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px; }

        .bg-in { background: #ecfdf5; color: var(--success); }
        .bg-out { background: #fff1f2; color: var(--danger); }
        .bg-profit { background: #eef2ff; color: var(--primary); }

        /* Table Card */
        .report-card {
            background: white;
            border-radius: 28px;
            padding: 30px;
            border: 1px solid #f1f5f9;
            margin-bottom: 30px;
        }

        .report-card h2 { font-size: 1.2rem; font-weight: 800; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        
        .table-res { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: var(--slate); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        
        .badge-method { 
            padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700;
            background: #f1f5f9; color: var(--slate);
        }

        @media (max-width: 768px) {
            .container { padding: 20px 15px; }
            .filter-card { padding: 15px; }
            .form-group { min-width: 100%; }
            .btn-filter { width: 100%; justify-content: center; }
        }

        @media print {
            nav, .filter-card, .btn-filter { display: none !important; }
            body { background: white; padding: 0; }
            .container { width: 100%; max-width: 100%; }
            .report-card { border: none; box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>

    <nav>
        <a href="menu_utama.php" class="brand">WARUNG <span>DARWOTO</span></a>
        <a href="menu_utama.php" class="btn-back">
            <span class="material-icons-round">arrow_back</span> Dashboard
        </a>
    </nav>

    <div class="container">
        
        <!-- Filter Form -->
        <div class="filter-card">
            <form class="filter-form" method="GET">
                <div class="form-group">
                    <label>Tanggal Awal</label>
                    <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>">
                </div>
                <div class="form-group">
                    <label>Tanggal Akhir</label>
                    <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>">
                </div>
                <button type="submit" class="btn-filter">
                    <span class="material-icons-round">filter_alt</span>
                    Tampilkan Data
                </button>
                <button type="button" class="btn-filter" style="background: var(--dark);" onclick="window.print()">
                    <span class="material-icons-round">print</span>
                    Cetak
                </button>
            </form>
        </div>

        <!-- Summary Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-in"><span class="material-icons-round">trending_up</span></div>
                <div class="stat-info">
                    <p>Total Pemasukan</p>
                    <h3>Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-out"><span class="material-icons-round">trending_down</span></div>
                <div class="stat-info">
                    <p>Total Pengeluaran</p>
                    <h3>Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-profit"><span class="material-icons-round">payments</span></div>
                <div class="stat-info">
                    <p>Laba / Rugi Bersih</p>
                    <h3 style="color: <?= $laba_rugi >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                        Rp <?= number_format($laba_rugi, 0, ',', '.') ?>
                    </h3>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 30px;">
            
            <!-- Tabel Pemasukan -->
            <div class="report-card">
                <h2><span class="material-icons-round" style="color:var(--success)">receipt_long</span> Rincian Pemasukan (Penjualan)</h2>
                <div class="table-res">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Nota</th>
                                <th>Tanggal</th>
                                <th>Metode</th>
                                <th style="text-align: right;">Total Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($data_pemasukan)): ?>
                                <tr><td colspan="4" style="text-align:center; color:var(--slate);">Tidak ada data pada periode ini</td></tr>
                            <?php endif; ?>
                            <?php foreach($data_pemasukan as $j): ?>
                                <tr>
                                    <td style="font-family:monospace; font-weight:700;"><?= $j['id_penjualan'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($j['tanggal'])) ?></td>
                                    <td><span class="badge-method"><?= $j['metode_bayar'] ?></span></td>
                                    <td style="text-align: right; font-weight:800;">Rp <?= number_format($j['total_bayar'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Pengeluaran -->
            <div class="report-card">
                <h2><span class="material-icons-round" style="color:var(--danger)">shopping_cart</span> Rincian Pengeluaran (Belanja)</h2>
                <div class="table-res">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Transaksi</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th style="text-align: right;">Total Keluar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($data_pengeluaran)): ?>
                                <tr><td colspan="4" style="text-align:center; color:var(--slate);">Tidak ada data pada periode ini</td></tr>
                            <?php endif; ?>
                            <?php foreach($data_pengeluaran as $p): ?>
                                <tr>
                                    <td style="font-family:monospace; font-weight:700;"><?= $p['id_pengeluaran'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($p['tanggal'])) ?></td>
                                    <td><?= $p['keterangan'] ?></td>
                                    <td style="text-align: right; font-weight:800; color:var(--danger);">Rp <?= number_format($p['total_pengeluaran'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</body>
</html>