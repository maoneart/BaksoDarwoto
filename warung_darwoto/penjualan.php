<?php
session_start();
include 'koneksi.php';
if(!isset($_SESSION['nama_user'])){ header("location:index.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kasir - Bakso Darwoto</title>
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
                radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.05) 0px, transparent 50%);
            padding-bottom: 50px;
        }

        /* Navigasi Atas */
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
            font-size: 1.1rem; 
            color: var(--dark); 
            letter-spacing: -1px;
            text-decoration: none;
        }
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

        /* Grid System */
        .main-grid { 
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 20px; 
            align-items: stretch;
        }
        
        @media (min-width: 992px) { 
            .main-grid { grid-template-columns: 360px 1fr; gap: 30px; } 
        }

        /* Card Style */
        .card { 
            background: white; 
            border-radius: 24px; 
            padding: 20px; 
            border: 1px solid #f1f5f9; 
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.02); 
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

        /* Form & Input Styling */
        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 0.7rem; font-weight: 700; color: var(--slate); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        input, select { 
            width: 100%; padding: 12px 14px; border: 2px solid #f1f5f9; 
            border-radius: 12px; font-size: 0.9rem; background: #fcfcfd; 
            color: var(--dark); transition: all 0.2s; outline: none;
            font-weight: 600;
        }
        input:focus, select:focus { border-color: var(--primary); background: white; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.05); }

        /* Badge ID Nota */
        .id-badge-box {
            background: var(--primary-light);
            border: 2px dashed #cbd5e1;
            padding: 12px;
            border-radius: 12px;
            text-align: center;
        }
        .id-text { 
            font-family: monospace; 
            font-size: 1rem; 
            font-weight: 800; 
            color: var(--primary); 
        }

        /* Item Row Styling */
        .item-row { 
            background: #ffffff; padding: 15px; border-radius: 20px; margin-bottom: 15px; 
            border: 1px solid #f1f5f9; position: relative; display: flex; flex-direction: column; gap: 12px;
            transition: 0.2s;
        }
        .item-row:hover { border-color: var(--primary); }

        @media (min-width: 768px) {
            .item-row { flex-direction: row; align-items: flex-end; padding-right: 60px; padding: 20px; }
            .col-menu { flex: 2; }
            .col-qty { width: 100px; }
            .col-sub { width: 180px; text-align: right; }
        }

        .btn-del { 
            position: absolute; top: 10px; right: 10px; 
            background: #fff1f2; color: var(--danger); border: none; 
            width: 30px; height: 30px; border-radius: 8px; 
            font-weight: 800; cursor: pointer; transition: 0.2s;
            display: flex; align-items: center; justify-content: center;
            z-index: 5;
        }
        .btn-del:hover { background: var(--danger); color: white; transform: rotate(90deg); }

        .sub-text { display: block; font-size: 1rem; font-weight: 800; color: var(--primary); margin-top: 5px; }

        .btn-add { 
            width: 100%; padding: 14px; border: 2px dashed #e2e8f0; border-radius: 16px; 
            background: white; color: var(--slate); font-weight: 700; cursor: pointer; transition: 0.2s;
            margin-top: 5px;
            margin-bottom: 30px; /* Jarak tambahan agar tidak mempet dengan total */
        }
        .btn-add:hover { background: var(--primary-light); border-color: var(--primary); color: var(--primary); }

        /* Summary Payment Section - Diperbarui agar lebih ramping */
        .payment-summary { 
            background: var(--dark); 
            color: white; 
            border-radius: 24px; 
            padding: 20px 30px; /* Padding dikurangi */
            margin-top: auto; 
            display: flex; 
            flex-direction: column;
            gap: 15px;
            box-shadow: 0 15px 30px -10px rgba(15, 23, 42, 0.3);
        }

        @media (min-width: 768px) {
            .payment-summary { flex-direction: row; justify-content: space-between; align-items: center; }
        }

        .total-info p { font-size: 0.65rem; opacity: 0.6; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; letter-spacing: 1px; }
        .total-info h2 { 
            font-size: 1.8rem; /* Ukuran teks dikurangi */
            font-weight: 800; 
            letter-spacing: -1px; 
            color: #ffffff !important;
            display: block !important;
        }
        
        @media (min-width: 768px) { .total-info h2 { font-size: 2.2rem; letter-spacing: -1.5px; } }

        .btn-pay { 
            background: var(--primary); color: white; border: none; padding: 14px 35px; /* Lebih ramping */
            border-radius: 14px; font-weight: 800; font-size: 0.95rem; cursor: pointer; 
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); transition: all 0.3s;
            width: 100%;
        }
        @media (min-width: 768px) { 
            .btn-pay { width: auto; border-radius: 16px; } 
        }
        .btn-pay:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(79, 70, 229, 0.4); }
    </style>
</head>
<body onload="initPage()">

<nav>
    <a href="menu_utama.php" class="brand">WARUNG <span>DARWOTO</span></a>
    <a href="menu_utama.php" class="btn-back">← Dashboard</a>
</nav>

<div class="container">
    <form action="simpan_penjualan.php" method="POST" id="formKasir">
        <div class="main-grid">
            
            <!-- INFO NOTA -->
            <div class="card">
                <h2>📦 Info Nota</h2>
                
                <div class="form-group">
                    <label>Tanggal Transaksi</label>
                    <input type="date" name="tanggal" id="tgl_pilih" value="<?= date('Y-m-d') ?>" onchange="updateID()">
                </div>

                <div class="form-group">
                    <label>ID Transaksi</label>
                    <div class="id-badge-box">
                        <input type="hidden" name="id_penjualan" id="id_penjualan_val">
                        <span class="id-text" id="id_penjualan_text">MEMUAT...</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <select name="metode_bayar" required>
                        <option value="Tunai">💵 Tunai (Cash)</option>
                        <option value="QRIS">📱 QRIS / Digital</option>
                        <option value="Transfer">💳 Transfer Bank</option>
                    </select>
                </div>

                <div style="margin-top: auto; padding: 20px; background: #f8fafc; border-radius: 15px; font-size: 0.8rem; color: var(--slate);">
                    <b>Catatan Kasir:</b><br>
                    Pastikan metode pembayaran sudah sesuai sebelum memproses transaksi.
                </div>
            </div>

            <!-- DAFTAR PESANAN -->
            <div class="card">
                <h2>🛒 Item Pesanan</h2>
                <div id="itemContainer" style="margin-bottom: 10px;">
                    <div class="item-row">
                        <div class="col-menu">
                            <label>Pilih Menu</label>
                            <select name="id_produk[]" class="pilih-p" onchange="handleMenuChange()" required>
                                <option value="" data-harga="0">-- Klik untuk Pilih --</option>
                                <?php
                                $produk = $conn->query("SELECT * FROM produk ORDER BY nama_produk ASC");
                                while($p = $produk->fetch()){
                                    echo "<option value='{$p['id_produk']}' data-harga='{$p['harga_jual']}'>{$p['nama_produk']} - Rp ".number_format($p['harga_jual'], 0, ',', '.')."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-qty">
                            <label>Jumlah</label>
                            <input type="number" name="jumlah[]" class="qty" value="1" min="1" oninput="updateTotal()">
                        </div>
                        <div class="col-sub">
                            <label>Subtotal</label>
                            <b class="sub-text">Rp 0</b>
                            <input type="hidden" name="subtotal[]" class="sub-val">
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-add" onclick="tambahRow()">+ TAMBAH BARIS MENU (F2)</button>

                <div class="payment-summary">
                    <div class="total-info">
                        <p>Total Tagihan</p>
                        <h2 id="grandTotalText">Rp 0</h2>
                        <input type="hidden" name="total_bayar" id="grandTotalVal">
                    </div>
                    <button type="submit" class="btn-pay">PROSES PEMBAYARAN</button>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
function initPage() {
    updateID();
    updateTotal();
}

function updateID() {
    const tgl = document.getElementById('tgl_pilih').value;
    const idText = document.getElementById('id_penjualan_text');
    const idVal = document.getElementById('id_penjualan_val');
    idText.innerText = "...";
    fetch('get_next_id.php?tgl=' + tgl)
        .then(response => response.text())
        .then(data => {
            idText.innerText = data;
            idVal.value = data;
        });
}

function handleMenuChange() {
    let allSelects = document.querySelectorAll('.pilih-p');
    let selectedValues = Array.from(allSelects).map(sel => sel.value).filter(val => val !== "");

    allSelects.forEach(select => {
        let currentValue = select.value;
        select.querySelectorAll('option').forEach(opt => {
            if (opt.value !== "" && opt.value !== currentValue && selectedValues.includes(opt.value)) {
                opt.disabled = true;
                opt.style.color = "#cbd5e1";
            } else {
                opt.disabled = false;
                opt.style.color = "inherit";
            }
        });
    });
    updateTotal();
}

function tambahRow() {
    const container = document.getElementById('itemContainer');
    const row = `
    <div class="item-row" style="animation: slideDown 0.3s ease-out;">
        <button type="button" class="btn-del" onclick="this.parentElement.remove(); handleMenuChange();">✕</button>
        <div class="col-menu">
            <label>Pilih Menu</label>
            <select name="id_produk[]" class="pilih-p" onchange="handleMenuChange()" required>
                <option value="" data-harga="0">-- Klik untuk Pilih --</option>
                <?php
                $produk = $conn->query("SELECT * FROM produk ORDER BY nama_produk ASC");
                while($p = $produk->fetch()){ echo "<option value='{$p['id_produk']}' data-harga='{$p['harga_jual']}'>{$p['nama_produk']} - Rp ".number_format($p['harga_jual'], 0, ',', '.')."</option>"; }
                ?>
            </select>
        </div>
        <div class="col-qty">
            <label>Jumlah</label>
            <input type="number" name="jumlah[]" class="qty" value="1" min="1" oninput="updateTotal()">
        </div>
        <div class="col-sub">
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
        const sel = row.querySelector('.pilih-p');
        const harga = parseFloat(sel.options[sel.selectedIndex]?.getAttribute('data-harga')) || 0;
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const sub = harga * qty;
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