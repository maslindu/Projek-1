<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi password
    if ($password != $confirm_password) {
        header("Location: ../register.php?error=Password tidak sama");
        exit;
    }

    // Ambil pengaturan kekuatan password
    $stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_name = 'password_strength'");
    $stmt->execute();
    $result = $stmt->get_result();
    $strength_setting = $result->fetch_assoc()['setting_value'] ?? 'medium';
    $stmt->close();

    // Validasi kekuatan password
    $error = validate_password($password, $strength_setting);
    if ($error) {
        header("Location: ../register.php?error=" . urlencode($error));
        exit;
    }


    // Cek apakah username sudah ada
    $check_username = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $check_username->bind_param("s", $username);
    $check_username->execute();
    $check_username->store_result();
    
    if ($check_username->num_rows > 0) {
        header("Location: ../register.php?error=Username sudah digunakan");
        exit;
    }

    // Cek apakah email sudah ada
    $check_email = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        header("Location: ../register.php?error=Email sudah digunakan");
        exit;
    }

    // Hash password dengan MD5
    $hashed_password = md5($password);

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    
    if ($stmt->execute()) {
        header("Location: ../login.php?success=Registrasi berhasil! Silakan login.");
    } else {
        header("Location: ../register.php?error=Registrasi gagal. Silakan coba lagi.");
    }
    
    $stmt->close();
    $conn->close();
}

function validate_password($password, $strength) {
    switch ($strength) {
        case 'high':
            if (strlen($password) < 8) {
                return "Password minimal 8 karakter";
            }
            if (!preg_match('/[A-Z]/', $password)) {
                return "Password harus mengandung huruf besar";
            }
            if (!preg_match('/[a-z]/', $password)) {
                return "Password harus mengandung huruf kecil";
            }
            if (!preg_match('/[0-9]/', $password)) {
                return "Password harus mengandung angka";
            }
            if (!preg_match('/[\W]/', $password)) {
                return "Password harus mengandung simbol";
            }
            break;
        case 'medium':
            if (strlen($password) < 6) {
                return "Password minimal 6 karakter";
            }
            if (!preg_match('/[A-Za-z]/', $password)) {
                return "Password harus mengandung huruf";
            }
            if (!preg_match('/[0-9]/', $password)) {
                return "Password harus mengandung angka";
            }
            break;
        case 'low':
            if (strlen($password) < 4) {
                return "Password minimal 4 karakter";
            }
            break;
    }
    return false;
}
?>