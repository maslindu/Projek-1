<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

// Cek role user
$isAdmin = ($_SESSION['role'] === 'admin');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="form_area dashboard-container">
            <h2 class="title">Selamat Datang</h2>
            <div class="welcome-message">
                <p>Halo, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <p>Role: <?php echo htmlspecialchars($_SESSION['role']); ?></p>
                <p>Anda telah berhasil login ke sistem.</p>
            </div>

            <?php if ($isAdmin): ?>
            <div class="admin-panel">
                <h3>Admin Panel</h3>
                <a href="#" class="admin-btn">Kelola User</a>
                <a href="#" class="admin-btn">Lihat Log</a>
                <a href="#" class="admin-btn">Pengaturan</a>
            </div>
            <?php else: ?>
            <div class="user-panel">
                <p>Selamat datang di dashboard user.</p>
                <a href="info_profile.php" class="admin-btn">Info Profil</a>
                <a href="change_password.php" class="admin-btn">Ubah Password</a>
            </div>
            <?php endif; ?>

            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
</body>
</html>
