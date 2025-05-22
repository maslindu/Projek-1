<?php
session_start();

// Cek apakah user sudah login dan admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'includes/config.php';

// Ambil pengaturan saat ini dari database
$settings = [];
$stmt = $conn->prepare("SELECT setting_name, setting_value FROM system_settings");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $settings[$row['setting_name']] = $row['setting_value'];
}
$stmt->close();

// Proses form jika ada submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi dan update session timeout
    if (isset($_POST['session_timeout']) && is_numeric($_POST['session_timeout'])) {
        $timeout = intval($_POST['session_timeout']);
        $stmt = $conn->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_name = 'session_timeout'");
        $stmt->bind_param("i", $timeout);
        $stmt->execute();
        $stmt->close();
        
        // Update session timeout langsung
        $_SESSION['timeout'] = $timeout;
    }

    // Validasi dan update password strength
    if (isset($_POST['password_strength']) && in_array($_POST['password_strength'], ['low', 'medium', 'high'])) {
        $strength = $_POST['password_strength'];
        $stmt = $conn->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_name = 'password_strength'");
        $stmt->bind_param("s", $strength);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect dengan pesan sukses
    header("Location: settings.php?success=Pengaturan berhasil diperbarui");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Sistem</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="form_area dashboard-container">
            <h2 class="title">Pengaturan Sistem</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            
            <form action="settings.php" method="POST">
                <div class="form_group">
                    <label for="session_timeout">Waktu Login (detik)</label>
                    <input type="number" id="session_timeout" name="session_timeout" 
                        class="plain-input"       
                        value="<?= htmlspecialchars($settings['session_timeout'] ?? '1800') ?>" 
                           min="60" max="86400" required>
                    <small>Waktu dalam detik sebelum sesi login berakhir (60-86400)</small>
                </div>
                
                <div class="form_group">
                    <label for="password_strength">Kekuatan Password Minimum</label>
                    <select id="password_strength" name="password_strength" class="form-control">
                        <option value="low" <?= ($settings['password_strength'] ?? 'medium') === 'low' ? 'selected' : '' ?>>Rendah (min 4 karakter)</option>
                        <option value="medium" <?= ($settings['password_strength'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Sedang (min 6 karakter, huruf + angka)</option>
                        <option value="high" <?= ($settings['password_strength'] ?? 'medium') === 'high' ? 'selected' : '' ?>>Tinggi (min 8 karakter, huruf besar/kecil + angka + simbol)</option>
                    </select>
                </div>
                
                <div class="profile-actions">
                    <button type="submit" class="btn edit-btn">Simpan Pengaturan</button>
                    <a href="dashboard.php" class="btn back-btn">Kembali ke Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>