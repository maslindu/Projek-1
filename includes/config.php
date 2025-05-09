<?php
// Konfigurasi Database
$host = "localhost";
$username = "root";
$password = "";
$database = "praktikum1php";

// Membuat koneksi
$conn = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?> 