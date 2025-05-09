<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password != $confirm_password) {
        echo "password tidak sama";
    } 
    $result = $conn->query()
    
    // buat perilaku ketika username sudah ada
    // buat perilaku ketika password tidak sama
    // buat perilaku ketika register berhasil
    // buat perilaku ketika register gagal
}
?> 