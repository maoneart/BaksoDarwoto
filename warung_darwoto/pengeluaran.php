<?php
session_start();
include 'koneksi.php';

// 1. Cek apakah sudah login
if(!isset($_SESSION['nama_user'])){ 
    header("location:index.php"); 
    exit(); 
}

// 2. Proteksi Role: Ambil role dan bersihkan spasi/huruf besar
$role = isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : 'kasir';

// 3. Jika role adalah kasir, tendang balik ke menu utama
if($role == 'kasir') {
    echo "<script>alert('Akses Ditolak! Hanya Admin yang dapat mencatat pengeluaran.'); window.location='menu_utama.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pengeluaran - Bakso Darwoto</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
                radial-gradient(at 100% 100%, rgba(244, 63, 94, 0.05) 0px, transparent 50%);
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

        .brand { font-weight: 800; font-size: 1.1rem; color: var(--dark); letter-spacing: -1px; text-decoration: none; }
        .brand span { color: var(--primary); }

        .btn-back {
            text-decoration: none;
            color: var(--slate);
            font-size: 0.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: 0.2s;
        }
        .btn-back:hover { color: var(--primary); }

        .container { max-width: 1250px; margin: 0 auto; padding: 20px; }

        .main-grid { 
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 20px; 
            align-items: stretch;
        }
        
        @media (min-width: 992px) { 
            .main-grid { grid-template-columns: 360px 1fr; gap: 30px; } 
        }

        .card { 
            background: white; 
            border-radius: 24px; 
            padding: 20px; 
            border: 1px solid #f1f5f9; 
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.02); 
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        @media (min-width: 768px) { .card { padding: 30px; } }

        .card h2 { 
            font-size: 1.1rem; 
            font-weight: 800; 
            margin-bottom: 20px; 
            color: var(--dark);
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 0.7rem; font-weight: 700; color: var(--slate); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        input, select { 
            width: 100%; padding: 12px 14px; border: 2px solid #f1f5f9; 
            border-radius: 12px; font-size: 0.9rem; background: #fcfcfd; 
            color: var(--dark); transition: all 0.2s; outline: none;
            font-weight: 600;
        }
        input:focus { border-color: var(--danger); background: white; box-shadow: 0 0 0 4px rgba(244, 63, 94, 0.05); }

        .id-badge-box {
            background: #fff1f2;
            border: 2px dashed #fecdd3;
            padding: 12px;
            border-radius: 12px;
            text-align: center;
        }
        .id-text { font-family: monospace; font-size: 1rem; font-weight: 800; color: var(--danger); }

        .item-row { 
            background: #ffffff; padding: 15px; border-radius: 20px; margin-bottom: 15px; 
            border: 1px solid #f1f5f9; position: relative; display: grid; grid-template-columns: 1fr; gap: 12px;
        }

        @media (min-width: 768px) {
            .item-row { grid-template-columns: 2fr 1fr 1fr 1.5fr 1.5fr; align-items: flex-end; padding-right: 50px; gap: 15px; }
        }

        .btn-del { 
            position: absolute; top: 10px; right: 10px; 
            background: #fff1f2; color: var(--danger); border: none; 
            width: 30px; height: 30px; border-radius: 8px; 
            font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center;
        }

        .sub-text { display: block; font-size: 0.95rem; font-weight: 800; color: var(--danger); margin-top: 5px; }

        .btn-add { 
            width: 100%; padding: 14px; border: 2px dashed #e2e8f0; border-radius: 16px; 
            background: white; color: var(--slate); font-weight: 700; cursor: pointer; transition: 0.2s;
            margin-bottom: 30px;
        }
        .btn-add:hover { background: #fff1f2; border-color: var(--danger); color: var(--danger); }

        .payment-summary { 
            background: var(--dark); 
            color: white; 
            border-radius: 24px; 
            padding: 20px 30px;
            margin-top: auto;
            display: flex; 
            flex-direction: column;
            gap: 15px;
            box-shadow: 0 15px 30px -10px rgba(15, 23, 42, 0.3);
        }
        @media (min-width: 768px) { .payment-summary { flex-direction: row; justify-content: space-between; align-items: center; } }

        .total-info p { font-size: 0.65rem; opacity: 0.6; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; letter-spacing: 1px; }
        .total-info h2 { font-size: 1.8rem; font-weight: 800; color: white !important; }

        .btn-pay { 
            background: var(--danger); color: white; border: none; padding: 14px 35px; 
            border-radius: 14px; font-weight: 800; font-size: 0.95rem; cursor: pointer; transition: 0.3s;
            width: 100%;
        }
        @media (min-width: 768px) { .btn-pay { width: auto; border-radius: 16px; } }
        .btn-pay:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(244, 63, 94, 0.4); }

        .table-res { overflow-x: auto; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: var(--slate); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.95rem; }
        
        .btn-action-del {
            background: #fff1f2; color: var(--danger); border: none; padding: 8px 12px;
            border-radius: 10px; font-size: 0.75rem; font-weight: 700; cursor: pointer;
            text-decoration: none; transition: 0.2s;
        }
        .btn-action-del:hover { background: var(--danger); color: white; }
    </style>
</head>
<body onload="initPage()">

<nav>
    <a href="menu_utama.php" class="brand">WARUNG <span>DARWOTO</span></a>
    <a href="menu_utama.php" class="btn-back">← Dashboard</a>
</nav>

<div class="container">
    <form action="simpan_pengeluaran.php" method="POST">
        <div class="main-grid">
            
            <!-- INFO PENGELUARAN -->
            <div class="card">
                <h2>📑 Info Pengeluaran</h2>
                <div class="form-group">
                    <label>Tanggal Nota</label>
                    <input type="date" name="tanggal" id="tgl_pilih" value="<?= date('Y-m-d') ?>" onchange="updateID()" required>
                </div>
                <div class="form-group">
                    <label>ID Pengeluaran</label>
                    <div class="id-badge-box">
                        <input type="hidden" name="id_pengeluaran" id="id_pengeluaran_val">
                        <span class="id-text" id="id_pengeluaran_text">...</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Keterangan / Lokasi</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Belanja Pasar, Warung Sembako, dll" required>
                </div>
                
                <div style="margin-top: auto; padding: 20px; background: #fef2f2; border-radius: 15px; font-size: 0.8rem; color: var(--danger); border: 1px dashed #fecdd3;">
                    <b>Catatan Pengeluaran:</b><br>
                    Harap simpan struk belanja fisik sebagai bukti pendukung laporan bulanan.
                </div>
            </div>

            <!-- DETAIL BARANG -->
            <div class="card">
                <h2>🛍️ Detail Barang Belanja</h2>
                <div id="itemContainer" style="margin-bottom: 10px;">
                    <div class="item-row">
                        <div>
                            <label>Nama Barang</label>
                            <input type="text" name="nama_barang[]" placeholder="Bawang, Daging, dll" required>
                        </div>
                        <div>
                            <label>Jumlah</label>
                            <input type="number" name="jumlah[]" class="qty" value="1" min="0.1" step="0.1" oninput="updateTotal()" required>
                        </div>
                        <div>
                            <label>Satuan</label>
                            <select name="satuan[]">
                                <option value="Kg">Kg</option>
                                <option value="Pcs">Pcs</option>
                                <option value="Liter">Liter</option>
                                <option value="Bks">Bks</option>
                            </select>
                        </div>
                        <div>
                            <label>Harga Satuan</label>
                            <input type="number" name="harga_satuan[]" class="price" placeholder="0" oninput="updateTotal()" required>
                        </div>
                        <div>
                            <label>Subtotal</label>
                            <b class="sub-text">Rp 0</b>
                            <input type="hidden" name="subtotal[]" class="sub-val">
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-add" onclick="tambahRow()">+ TAMBAH BARANG (F2)</button>

                <div class="payment-summary">
                    <div class="total-info">
                        <p>Total Pengeluaran</p>
                        <h2 id="grandTotalText">Rp 0</h2>
                        <input type="hidden" name="total_pengeluaran" id="grandTotalVal">
                    </div>
                    <button type="submit" class="btn-pay">SIMPAN PENGELUARAN</button>
                </div>
            </div>

        </div>
    </form>

    <div class="card" style="margin-top: 30px;">
        <h2>📋 Riwayat Pengeluaran Terakhir</h2>
        <div class="table-res">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = $conn->query("SELECT TOP 10 * FROM pengeluaran_header ORDER BY tanggal DESC, id_pengeluaran DESC");
                    while ($row = $query->fetch()) {
                        echo "<tr>
                            <td style='font-family:monospace; font-weight:700;'>{$row['id_pengeluaran']}</td>
                            <td>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>
                            <td>{$row['keterangan']}</td>
                            <td style='font-weight:800;'>Rp " . number_format($row['total_pengeluaran'], 0, ',', '.') . "</td>
                            <td>
                                <a href='hapus_pengeluaran.php?id={$row['id_pengeluaran']}' class='btn-action-del' onclick='return confirm(\"Hapus transaksi ini?\")'>Hapus</a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function initPage() { updateID(); updateTotal(); }

function updateID() {
    const tgl = document.getElementById('tgl_pilih').value;
    const idText = document.getElementById('id_pengeluaran_text');
    const idVal = document.getElementById('id_pengeluaran_val');
    idText.innerText = "Memuat...";
    fetch('get_next_pengeluaran_id.php?tgl=' + tgl)
        .then(res => res.text())
        .then(data => {
            idText.innerText = data;
            idVal.value = data;
        });
}

function tambahRow() {
    const container = document.getElementById('itemContainer');
    const row = `
    <div class="item-row" style="animation: slideDown 0.3s ease-out;">
        <button type="button" class="btn-del" onclick="this.parentElement.remove(); updateTotal();">✕</button>
        <div>
            <label>Nama Barang</label>
            <input type="text" name="nama_barang[]" required>
        </div>
        <div>
            <label>Jumlah</label>
            <input type="number" name="jumlah[]" class="qty" value="1" min="0.1" step="0.1" oninput="updateTotal()" required>
        </div>
        <div>
            <label>Satuan</label>
            <select name="satuan[]">
                <option value="Kg">Kg</option><option value="Pcs">Pcs</option><option value="Liter">Liter</option><option value="Bks">Bks</option>
            </select>
        </div>
        <div>
            <label>Harga Satuan</label>
            <input type="number" name="harga_satuan[]" class="price" oninput="updateTotal()" required>
        </div>
        <div>
            <label>Subtotal</label>
            <b class="sub-text">Rp 0</b>
            <input type="hidden" name="subtotal[]" class="sub-val">
        </div>
    </div>`;
    container.insertAdjacentHTML('beforeend', row);
}

function updateTotal() {
    let grand = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const price = parseFloat(row.querySelector('.price').value) || 0;
        const sub = qty * price;
        row.querySelector('.sub-text').innerText = "Rp " + sub.toLocaleString('id-ID');
        row.querySelector('.sub-val').value = sub;
        grand += sub;
    });
    document.getElementById('grandTotalText').innerText = "Rp " + grand.toLocaleString('id-ID');
    document.getElementById('grandTotalVal').value = grand;
}

document.addEventListener('keydown', e => { if(e.key === "F2") { e.preventDefault(); tambahRow(); } });
</script>

<style>
@keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>
</body>
</html>