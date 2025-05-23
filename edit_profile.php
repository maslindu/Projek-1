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
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Proses form edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    
    // Validasi input
    if (empty($new_username) || empty($new_email)) {
        $error = "Username dan email tidak boleh kosong";
    } else {
        // Cek apakah username sudah digunakan oleh user lain
        $check_username = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $check_username->bind_param("si", $new_username, $_SESSION['user_id']);
        $check_username->execute();
        $check_username->store_result();
        
        if ($check_username->num_rows > 0) {
            $error = "Username sudah digunakan";
        } else {
            // Cek apakah email sudah digunakan oleh user lain
            $check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $check_email->bind_param("si", $new_email, $_SESSION['user_id']);
            $check_email->execute();
            $check_email->store_result();
            
            if ($check_email->num_rows > 0) {
                $error = "Email sudah digunakan";
            } else {
                // Update data user
                $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $update_stmt->bind_param("ssi", $new_username, $new_email, $_SESSION['user_id']);
                
                if ($update_stmt->execute()) {
                    // Update session
                    $_SESSION['username'] = $new_username;
                    
                    // Catat aktivitas edit profil
                    require_once 'includes/log_activity.php';
                    log_activity($_SESSION['user_id'], $new_username, 'profile_update', 'User updated profile (Email: ' . $new_email . ')');
                    
                    // Redirect ke info profile dengan pesan sukses
                    header("Location: info_profile.php?success=Profil berhasil diperbarui");
                    exit;
                } else {
                    $error = "Gagal memperbarui profil. Silakan coba lagi.";
                }
                
                $update_stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="form_area profile-container">
            <h2 class="title">Edit Profil</h2>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form action="edit_profile.php" method="POST">
                <div class="form_group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" 
                           value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="form_group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                
                <div class="profile-actions">
                    <button type="submit" class="btn edit-btn">Simpan Perubahan</button>
                    <a href="info_profile.php" class="btn back-btn">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>