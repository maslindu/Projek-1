<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $role = 'user'; 

    $check_usn_query = "SELECT * FROM user WHERE username = '$username'";
    $res = mysqli_query($conn, $check_usn_query);

    if (mysqli_num_rows($res) > 0) {
        die("Error: Username telah terpakai.");
    }

    if ($password != $confirm_password) {
        die("Error: Password tidak sama.");
    }

    $insert_query = "INSERT INTO user (username, email, password, role)
                     VALUES ('$username', '$email', '$password', '$role')";

    if (mysqli_query($conn, $insert_query)) {
        echo "Registrasi berhasil.";
    } else {
        echo "Registrasi gagal: " . mysqli_error($conn);
    }
}

