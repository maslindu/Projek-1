<?php
session_start();

// Cek apakah user sudah login dan admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'includes/config.php';

// Ambil semua user dari database
$users = [];
$stmt = $conn->prepare("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$stmt->close();

// Proses aksi (hapus/promote/demote)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];
    
    // Validasi aksi
    if ($action === 'delete') {
        // Jangan izinkan menghapus diri sendiri
        if ($user_id != $_SESSION['user_id']) {
            // Dapatkan info user yang akan dihapus untuk log
            $stmt_get_user = $conn->prepare("SELECT username FROM users WHERE id = ?");
            $stmt_get_user->bind_param("i", $user_id);
            $stmt_get_user->execute();
            $user_to_delete = $stmt_get_user->get_result()->fetch_assoc();
            $stmt_get_user->close();
            
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();
            
            // Catat aktivitas penghapusan
            require_once 'includes/log_activity.php';
            log_activity(
                $_SESSION['user_id'], 
                $_SESSION['username'], 
                'delete_user', 
                'Admin menghapus user: ' . $user_to_delete['username'] . ' (ID: ' . $user_id . ')'
            );
            
            // Refresh halaman
            header("Location: manage_users.php?success=User berhasil dihapus");
            exit;
        }
    } elseif ($action === 'promote' || $action === 'demote') {
        $new_role = ($action === 'promote') ? 'admin' : 'user';
        
        // Jangan izinkan mengubah role diri sendiri
        if ($user_id != $_SESSION['user_id']) {
            // Dapatkan info user yang akan diubah
            $stmt_get_user = $conn->prepare("SELECT username, role FROM users WHERE id = ?");
            $stmt_get_user->bind_param("i", $user_id);
            $stmt_get_user->execute();
            $user_to_update = $stmt_get_user->get_result()->fetch_assoc();
            $stmt_get_user->close();
            
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->bind_param("si", $new_role, $user_id);
            $stmt->execute();
            $stmt->close();
            
            // Catat aktivitas perubahan role
            require_once 'includes/log_activity.php';
            $action_type = ($action === 'promote') ? 'promote_user' : 'demote_user';
            $action_desc = ($action === 'promote') ? 'menaikkan' : 'menurunkan';
            log_activity(
                $_SESSION['user_id'], 
                $_SESSION['username'], 
                $action_type, 
                'Admin ' . $action_desc . ' role user ' . $user_to_update['username'] . 
                ' (ID: ' . $user_id . ') dari ' . $user_to_update['role'] . ' ke ' . $new_role
            );
            
            // Refresh halaman
            header("Location: manage_users.php?success=Role user berhasil diubah");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="form_area dashboard-container">
            <h2 class="title">Kelola User</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            
            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                            <td class="actions">
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <?php if ($user['role'] === 'user'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <input type="hidden" name="action" value="promote">
                                            <button type="submit" class="btn small-btn promote-btn">Promote</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <input type="hidden" name="action" value="demote">
                                            <button type="submit" class="btn small-btn demote-btn">Demote</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn small-btn delete-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">Hapus</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <a href="dashboard.php" class="btn back-btn">Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>