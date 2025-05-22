<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash password input dengan MD5
    $hashed_password = md5($password);

    // Cari pengguna berdasarkan username dan password yang sudah dihash
    $stmt = $conn->prepare("SELECT id, username, role FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $hashed_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Username atau password salah
        header("Location: ../login.php?error=Username atau password salah");
        exit;
    }

    $user = $result->fetch_assoc();

    // Login berhasil, set session
    $_SESSION["user_id"] = $user['id'];
    $_SESSION["username"] = $user['username'];
    $_SESSION["role"] = $user['role'];
    $_SESSION["logged_in"] = true;

    // Jika user adalah admin, set session timeout sangat lama (10 tahun)
    if ($user['role'] === 'admin') {
        $_SESSION['timeout'] = time() + (10 * 365 * 24 * 60 * 60); // 10 tahun
    } else {
        // Ambil pengaturan timeout sesi untuk user biasa
        $stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_name = 'session_timeout'");
        $stmt->execute();
        $result = $stmt->get_result();
        $timeout = intval($result->fetch_assoc()['setting_value'] ?? 1800);
        $stmt->close();
        $_SESSION['timeout'] = time() + $timeout;
    }

    // Redirect ke dashboard
    header("Location: ../dashboard.php");
    exit;
}
?>