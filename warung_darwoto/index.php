<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Warung Bakso Darwoto</title>
    <style>
        /* Reset & Base Style */
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Menggunakan min-height agar tidak terpotong di layar pendek */
            margin: 0;
            padding: 20px; /* Jarak aman untuk mobile */
        }

        .login-container {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px; /* Lebar maksimal sedikit diperbesar */
            text-align: center;
            transition: transform 0.3s ease;
        }

        /* Responsive Adjustment untuk Mobile */
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .login-container h2 {
                font-size: 24px;
            }
        }

        .login-container h2 {
            margin: 0 0 10px 0;
            color: #333;
            font-weight: 700;
        }

        .login-container p {
            color: #777;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }

        input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px; /* Font 16px mencegah auto-zoom di iOS Safari */
            transition: border-color 0.3s, box-shadow 0.3s;
            outline: none;
        }

        input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        button {
            width: 100%;
            padding: 14px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s, transform 0.1s;
            margin-top: 10px;
        }

        button:hover {
            background: #0056b3;
        }

        button:active {
            transform: scale(0.98);
        }

        .alert {
            background: #fff5f5;
            color: #c53030;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid #feb2b2;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .db-info {
            margin-top: 25px;
            font-size: 11px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Bakso Darwoto</h2>
    <p>Sistem Informasi Warung</p>

    <?php if(isset($_GET['pesan']) && $_GET['pesan'] == "gagal"): ?>
        <div class="alert">
            Username atau Password salah!
        </div>
    <?php endif; ?>

    <form action="proses_login.php" method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Masukkan username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Masukkan password" required>
        </div>
        <button type="submit">MASUK KE SISTEM</button>
    </form>
    
    <div class="db-info">
        Database terhubung: <strong>MS Access (.mdb)</strong>
    </div>
</div>

</body>
</html>