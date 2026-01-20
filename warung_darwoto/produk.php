<?php
session_start();
include 'koneksi.php';

// 1. Cek Login: Pastikan user sudah login
if(!isset($_SESSION['nama_user'])){
    header("location:index.php");
    exit();
}

// 2. Proteksi Role: Hanya Admin yang boleh akses halaman ini
$role = isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : 'kasir';
if($role == 'kasir') {
    echo "<script>alert('Akses Ditolak! Kasir tidak diizinkan mengelola data produk.'); window.location='menu_utama.php';</script>";
    exit();
}

$edit_mode = false;
$edit_data = ['id_produk' => '', 'nama_produk' => '', 'harga_jual' => '', 'kategori' => ''];

// Logic Ambil Data untuk Edit
if (isset($_GET['edit_id'])) {
    $edit_mode = true;
    $stmt = $conn->prepare("SELECT * FROM produk WHERE id_produk = ?");
    $stmt->execute([$_GET['edit_id']]);
    $edit_data = $stmt->fetch();
}

// Logika Simpan / Update Data
if (isset($_POST['simpan'])) {
    $id_produk = strtoupper($_POST['id_produk']);
    $nama_produk = $_POST['nama_produk'];
    // Bersihkan titik dari format ribuan sebelum masuk ke database
    $harga_jual = str_replace('.', '', $_POST['harga_jual']);
    $kategori = $_POST['kategori'];

    try {
        if (isset($_POST['is_edit']) && $_POST['is_edit'] == '1') {
            $sql = "UPDATE produk SET harga_jual = ? WHERE id_produk = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$harga_jual, $id_produk]);
            $msg = "Harga Produk Berhasil Diperbarui!";
        } else {
            $sql = "INSERT INTO produk (id_produk, nama_produk, harga_jual, kategori) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id_produk, $nama_produk, $harga_jual, $kategori]);
            $msg = "Produk Berhasil Ditambahkan!";
        }
        echo "<script>alert('$msg'); window.location='produk.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Gagal! Cek Database atau ID Produk sudah ada.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Master Produk - Bakso Darwoto</title>
    <!-- Google Fonts & Material Icons -->
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
        .main-grid { display: grid; grid-template-columns: 1fr; gap: 30px; }
        @media (min-width: 992px) { .main-grid { grid-template-columns: 380px 1fr; } }

        .card { 
            background: white; 
            border-radius: 28px; 
            padding: 30px; 
            border: 1px solid #f1f5f9; 
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.02); 
        }
        .card h2 { 
            font-size: 1.25rem; 
            font-weight: 800; 
            margin-bottom: 25px; 
            color: var(--dark);
            letter-spacing: -0.5px;
        }

        .form-group { margin-bottom: 20px; }
        label { display: block; font-size: 0.75rem; font-weight: 700; color: var(--slate); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        input, select { 
            width: 100%; padding: 12px 16px; border: 2px solid #f1f5f9; 
            border-radius: 14px; font-size: 0.95rem; background: #fcfcfd; 
            color: var(--dark); transition: all 0.2s; outline: none;
        }
        input:focus, select:focus { border-color: var(--primary); background: white; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.05); }
        input[readonly] { background: #f1f5f9; cursor: not-allowed; color: #94a3b8; }
        
        .btn-save { 
            width: 100%; padding: 15px; background: var(--primary); color: white; 
            border: none; border-radius: 14px; font-weight: 800; font-size: 1rem;
            cursor: pointer; transition: 0.3s; margin-top: 10px;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 15px 20px -5px rgba(79, 70, 229, 0.4); }
        .btn-cancel {
            display: block; width: 100%; text-align: center; padding: 12px;
            margin-top: 10px; text-decoration: none; color: var(--slate);
            font-weight: 700; font-size: 0.85rem;
        }

        .table-res { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: var(--slate); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; vertical-align: middle; }

        .badge { padding: 6px 12px; border-radius: 10px; font-size: 0.75rem; font-weight: 700; }
        .makanan { background: #eef2ff; color: var(--primary); }
        .minuman { background: #ecfdf5; color: var(--success); }
        .kode-text { font-family: monospace; font-weight: 700; color: var(--primary); background: var(--primary-light); padding: 4px 8px; border-radius: 8px; }

        /* Action Buttons Styling */
        .btn-edit { 
            color: var(--primary); 
            background: var(--primary-light);
            text-decoration: none; 
            width: 38px; height: 38px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 10px;
            transition: 0.2s;
            margin-right: 5px;
        }
        .btn-edit:hover { background: var(--primary); color: white; }

        .btn-del { 
            color: var(--danger); 
            background: #fff1f2;
            text-decoration: none; 
            width: 38px; height: 38px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 10px;
            transition: 0.2s;
        }
        .btn-del:hover { background: var(--danger); color: white; }

        .mobile-list { display: none; flex-direction: column; gap: 15px; }
        .item-card { 
            background: #ffffff; padding: 20px; border-radius: 20px; 
            display: flex; justify-content: space-between; align-items: center;
            border: 1px solid #f1f5f9;
        }
        @media (max-width: 767px) {
            .desktop-table { display: none; }
            .mobile-list { display: flex; }
        }

        .item-info b { display: block; font-size: 1rem; color: var(--dark); margin-bottom: 4px; }
        .item-info span { font-size: 0.75rem; color: var(--slate); font-weight: 600; }
        .item-actions { margin-top: 15px; display: flex; gap: 10px; }
    </style>
</head>
<body>

    <nav>
        <a href="menu_utama.php" class="brand">WARUNG <span>DARWOTO</span></a>
        <a href="menu_utama.php" class="btn-back">
            <span class="material-icons-round" style="font-size: 1.2rem;">arrow_back</span>
            Dashboard
        </a>
    </nav>

    <div class="container">
        <div class="main-grid">
            <!-- FORM SEKSI -->
            <div class="card">
                <h2><?= $edit_mode ? 'Edit Harga Menu' : 'Tambah Menu' ?></h2>
                <form method="POST">
                    <?php if($edit_mode): ?>
                        <input type="hidden" name="is_edit" value="1">
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Kode Produk (ID)</label>
                        <input type="text" name="id_produk" value="<?= $edit_data['id_produk'] ?>" <?= $edit_mode ? 'readonly' : '' ?> placeholder="Contoh: BUR001" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Menu</label>
                        <input type="text" name="nama_produk" value="<?= $edit_data['nama_produk'] ?>" <?= $edit_mode ? 'readonly' : '' ?> placeholder="Nama Bakso / Minuman" required>
                    </div>
                    <div class="form-group">
                        <label>Harga Jual (Rp)</label>
                        <input type="text" name="harga_jual" id="harga_jual" value="<?= $edit_data['harga_jual'] !== '' ? number_format((float)$edit_data['harga_jual'], 0, ',', '.') : '' ?>" placeholder="0" required autofocus>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <?php if($edit_mode): ?>
                            <input type="text" value="<?= $edit_data['kategori'] ?>" readonly>
                            <input type="hidden" name="kategori" value="<?= $edit_data['kategori'] ?>">
                        <?php else: ?>
                            <select name="kategori">
                                <option value="Makanan">Makanan</option>
                                <option value="Minuman">Minuman</option>
                            </select>
                        <?php endif; ?>
                    </div>
                    <button type="submit" name="simpan" class="btn-save">
                        <span class="material-icons-round"><?= $edit_mode ? 'update' : 'add_task' ?></span>
                        <?= $edit_mode ? 'Update Harga' : 'Simpan ke Database' ?>
                    </button>
                    <?php if($edit_mode): ?>
                        <a href="produk.php" class="btn-cancel">Batal Edit</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- DAFTAR SEKSI -->
            <div class="card">
                <h2>Daftar Produk</h2>
                
                <div class="table-res desktop-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Menu</th>
                                <th>Harga</th>
                                <th>Kategori</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = $conn->query("SELECT * FROM produk ORDER BY id_produk ASC");
                            while ($row = $query->fetch()) {
                                $catClass = ($row['kategori'] == 'Makanan') ? 'makanan' : 'minuman';
                                echo "<tr>
                                        <td><span class='kode-text'>{$row['id_produk']}</span></td>
                                        <td style='font-weight:600;'>{$row['nama_produk']}</td>
                                        <td style='font-weight:800;'>Rp " . number_format($row['harga_jual'], 0, ',', '.') . "</td>
                                        <td><span class='badge {$catClass}'>{$row['kategori']}</span></td>
                                        <td>
                                            <a href='produk.php?edit_id={$row['id_produk']}' class='btn-edit' title='Edit Harga'>
                                                <span class='material-icons-round'>edit</span>
                                            </a>
                                            <a href='hapus_produk.php?id={$row['id_produk']}' class='btn-del' title='Hapus Produk' onclick='return confirm(\"Hapus produk ini?\")'>
                                                <span class='material-icons-round'>delete_outline</span>
                                            </a>
                                        </td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="mobile-list">
                    <?php
                    $query = $conn->query("SELECT * FROM produk ORDER BY id_produk ASC");
                    while ($row = $query->fetch()) {
                        $catClass = ($row['kategori'] == 'Makanan') ? 'makanan' : 'minuman';
                        echo "
                        <div class='item-card'>
                            <div class='item-info'>
                                <span>{$row['id_produk']}</span>
                                <b>{$row['nama_produk']}</b>
                                <span class='badge {$catClass}'>{$row['kategori']}</span>
                                <div class='item-actions'>
                                    <a href='produk.php?edit_id={$row['id_produk']}' class='btn-edit'>
                                        <span class='material-icons-round'>edit</span>
                                    </a>
                                    <a href='hapus_produk.php?id={$row['id_produk']}' class='btn-del' onclick='return confirm(\"Hapus?\")'>
                                        <span class='material-icons-round'>delete_outline</span>
                                    </a>
                                </div>
                            </div>
                            <div class='item-price'>
                                <span class='price'>Rp " . number_format($row['harga_jual'], 0, ',', '.') . "</span>
                            </div>
                        </div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk format ribuan dengan titik
        const hargaInput = document.getElementById('harga_jual');
        
        hargaInput.addEventListener('keyup', function(e) {
            // Hilangkan semua karakter kecuali angka
            let value = this.value.replace(/[^0-9]/g, '');
            
            // Format ulang dengan Intl.NumberFormat bahasa Indonesia
            if (value) {
                this.value = new Intl.NumberFormat('id-ID').format(value);
            } else {
                this.value = '';
            }
        });
    </script>
</body>
</html>