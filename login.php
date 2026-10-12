<?php
session_start();

if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged'] = true;
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "ID Pentadbir atau Kata Laluan Salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk Admin - SKY29 Lounge</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; margin: 0; padding: 0; }
        body {
            background-color: #050914;
            background-image: 
                radial-gradient(circle at 50% 20%, rgba(56, 189, 248, 0.25) 0%, transparent 60%),
                linear-gradient(rgba(5, 9, 20, 0.85), rgba(5, 9, 20, 0.92)),
                url('cityscape-of-george-town-2026-03-17-00-01-12-utc.jpg');
            background-position: center; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;
            color: #FFFFFF; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .login-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), rgba(56, 189, 248, 0.05));
            backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
            border-radius: 28px; border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 32px; width: 100%; max-width: 400px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 12px; margin-bottom: 8px; color: rgba(255,255,255,0.8); }
        .form-group input {
            width: 100%; padding: 14px 18px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.1); color: #fff; font-size: 13px; outline: none;
        }
        .btn-login {
            width: 100%; padding: 14px; background: linear-gradient(135deg, #38BDF8, #0284C7);
            border: none; border-radius: 16px; color: #fff; font-weight: 600; cursor: pointer; font-size: 14px; margin-top: 10px;
        }
        .error-msg { background: rgba(239, 68, 68, 0.2); border: 1px solid #EF4444; color: #FCA5A5; padding: 10px; border-radius: 12px; font-size: 12px; margin-bottom: 16px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-card">
        <a href="index.php" style="color:#38BDF8; font-size:12px; text-decoration:none; display:inline-block; margin-bottom:16px;">← Kembali ke Utama</a>
        <h2 style="margin-bottom:6px;">Portal Admin</h2>
        <p style="font-size:12px; color:rgba(255,255,255,0.6); margin-bottom:24px;">Sila log masuk untuk menguruskan tempahan.</p>
        
        <?php if(!empty($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>ID Pentadbir</label>
                <input type="text" name="username" placeholder="Masukkan ID Admin" required>
            </div>
            <div class="form-group">
                <label>Kata Laluan</label>
                <input type="password" name="password" placeholder="Masukkan Kata Laluan" required>
            </div>
            <button type="submit" class="btn-login">Log Masuk</button>
        </form>
    </div>
</body>
</html>
