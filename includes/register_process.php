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
?>