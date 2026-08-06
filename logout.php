<?php
session_start();

// 1. Padam semua pembolehubah sesi (session variables)
$_SESSION = array();

// 2. Musnahkan sesi log masuk
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// 3. Bawa admin kembali ke halaman Log Masuk
header("Location: login.php");
exit;
?>