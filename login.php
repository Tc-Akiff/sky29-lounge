<?php
session_start();

// 1. Jika admin sudah log masuk, terus bawa ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit;
}

// 2. Semakan apabila butang "Log Masuk" ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Maklumat akaun Admin
    if ($username === "admin" && $password === "admin123") {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk Admin - Lounge SKY29</title>
    
    <!-- Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: #0b1329;
            /* Latar belakang dengan 2 lapisan FLARE BIRU MUDA BERSINAR */
            background-image: 
                radial-gradient(circle at 50% 38%, rgba(56, 189, 248, 0.45) 0%, rgba(14, 165, 233, 0.15) 35%, transparent 65%),
                radial-gradient(circle at 20% 80%, rgba(56, 189, 248, 0.25) 0%, transparent 45%),
                linear-gradient(rgba(11, 19, 41, 0.35), rgba(11, 19, 41, 0.5)),
                url('cityscape-of-george-town-2026-03-17-00-01-12-utc.jpg');
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* REKA BENTUK LIQUID GLASS iOS */
        .login-card {
            position: relative;
            background: linear-gradient(
                135deg, 
                rgba(255, 255, 255, 0.28) 0%, 
                rgba(56, 189, 248, 0.08) 50%,
                rgba(255, 255, 255, 0.15) 100%
            );
            backdrop-filter: blur(35px) saturate(210%);
            -webkit-backdrop-filter: blur(35px) saturate(210%);
            padding: 42px 36px;
            border-radius: 36px;
            
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-top: 1px solid rgba(255, 255, 255, 0.75);
            border-left: 1px solid rgba(255, 255, 255, 0.5);

            box-shadow: 
                0 30px 60px -12px rgba(0, 0, 0, 0.5),
                0 0 40px rgba(56, 189, 248, 0.35),
                inset 0 1px 2px 0 rgba(255, 255, 255, 0.7),
                inset 0 -2px 4px 0 rgba(0, 0, 0, 0.15);

            width: 100%;
            max-width: 390px;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: -40%;
            left: -40%;
            width: 180%;
            height: 180%;
            background: radial-gradient(
                circle at 30% 20%, 
                rgba(56, 189, 248, 0.3) 0%, 
                transparent 50%
            );
            pointer-events: none;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .login-header h3 {
            color: #FFFFFF;
            font-size: 23px;
            font-weight: 700;
            letter-spacing: -0.3px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3), 0 0 15px rgba(56, 189, 248, 0.5);
        }

        .login-header p {
            color: rgba(255, 255, 255, 0.88);
            font-size: 12.5px;
            margin-top: 4px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.95);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        .form-group input {
            width: 100%;
            padding: 13px 18px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-top: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 18px;
            font-size: 13.5px;
            outline: none;
            background: rgba(255, 255, 255, 0.18);
            color: #FFFFFF;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-group input:focus {
            background: rgba(255, 255, 255, 0.32);
            border-color: #38BDF8;
            box-shadow: 
                0 0 20px rgba(56, 189, 248, 0.5),
                inset 0 1px 2px rgba(255, 255, 255, 0.6);
        }

        .btn-submit {
            position: relative;
            z-index: 1;
            width: 100%;
            padding: 14px;
            background: linear-gradient(
                135deg, 
                #38BDF8 0%, 
                #0284C7 100%
            );
            color: #FFFFFF;
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 18px;
            font-weight: 600;
            font-size: 14.5px;
            cursor: pointer;
            box-shadow: 
                0 10px 25px -5px rgba(56, 189, 248, 0.6),
                inset 0 1px 1px rgba(255, 255, 255, 0.8);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: scale(1.02);
            box-shadow: 
                0 14px 30px -5px rgba(56, 189, 248, 0.8),
                inset 0 1px 2px rgba(255, 255, 255, 0.9);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .error-msg {
            position: relative;
            z-index: 1;
            background: rgba(239, 68, 68, 0.35);
            backdrop-filter: blur(12px);
            color: #FFFFFF;
            padding: 11px 16px;
            border-radius: 16px;
            font-size: 12px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        .btn-back-home {
            position: relative;
            z-index: 1;
            display: block;
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-back-home:hover {
            color: #38BDF8;
            text-shadow: 0 0 12px rgba(56, 189, 248, 0.8);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <h3>Portal Admin</h3>
            <p>Sistem Tempahan Lounge SKY29</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-msg">⚠️ <?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Kata Laluan</label>
                <input type="password" name="password" placeholder="Masukkan kata laluan" required>
            </div>
            <button type="submit" class="btn-submit">Log Masuk</button>
        </form>

        <a href="index.php" class="btn-back-home">← Kembali ke Kalendar Utama</a>
    </div>

</body>
</html>