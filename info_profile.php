<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['logged_in']) || !isset($_SESSION['timeout']) || $_SESSION['timeout'] < time()) {
    // Hapus session jika timeout
    session_unset();
    session_destroy();
    header("Location: login.php?error=Sesi telah berakhir, silakan login kembali");
    exit;
}

// Perbarui timeout sesi
$_SESSION['timeout'] = time() + ($_SESSION['timeout'] - time());

require_once 'includes/config.php';

// Ambil data user dari database
$stmt = $conn->prepare("SELECT username, email, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Profil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="form_area profile-container">
            <h2 class="title">Info Profil</h2>
            
            <!-- Gambar profil -->
            <div class="profile-picture">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['username']) ?>&background=DE5499&color=fff&size=128" alt="Profile Picture" class="profile-img">
            </div>
            
            <!-- Informasi profil -->
            <div class="profile-info">
                <div class="info-item">
                    <span class="info-label">Username</span>
                    <span class="info-value"><?= htmlspecialchars($user['username']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Role</span>
                    <span class="info-value"><?= htmlspecialchars($user['role']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Bergabung sejak</span>
                    <span class="info-value"><?= date('d F Y', strtotime($user['created_at'])) ?></span>
                </div>
            </div>
            
            <!-- Tombol aksi -->
            <div class="profile-actions">
                <a href="edit_profile.php" class="btn edit-btn">Edit Profil</a>
                <a href="dashboard.php" class="btn back-btn">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>