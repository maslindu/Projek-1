<?php
session_start();

// Catat aktivitas logout jika user sudah login
if (isset($_SESSION['user_id'])) {
    require_once 'includes/log_activity.php';
    log_activity($_SESSION['user_id'], $_SESSION['username'], 'logout', 'User logged out');
}

// Hapus semua data session
$_SESSION = array();

// Hapus session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}


// Hancurkan session
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit;
?>