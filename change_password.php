<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Semua field harus diisi";
    } elseif ($new_password != $confirm_password) {
        $error = "Password baru tidak sama";
    } else {
        // Verifikasi password saat ini
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (md5($current_password) != $user['password']) {
            $error = "Password saat ini salah";
        } else {
            // Update password
            $hashed_password = md5($new_password);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_password, $_SESSION['user_id']);
            
            if ($update_stmt->execute()) {
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
                
                // Redirect ke halaman login dengan pesan sukses
                header("Location: login.php?success=Password berhasil diubah. Silakan login kembali.");
                exit;
            } else {
                $error = "Gagal mengubah password";
            }
            
            $update_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="form_area profile-container">
            <h2 class="title">Ubah Password</h2>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <form action="change_password.php" method="POST">
                <div class="form_group">
                    <label for="current_password">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form_group">
                    <label for="new_password">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form_group">
                    <label for="confirm_password">Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="profile-actions">
                    <button type="submit" class="btn edit-btn">Simpan Password</button>
                    <a href="dashboard.php" class="btn back-btn">Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>