<?php
session_start();

// Cek apakah user sudah login dan admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once 'includes/config.php';

// Proses penghapusan log jika ada request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'clear_logs') {
    $stmt = $conn->prepare("TRUNCATE TABLE activity_log");
    $stmt->execute();
    $stmt->close();
    
    header("Location: view_logs.php?success=Logs cleared successfully");
    exit;
}

// Ambil semua log dari database
$logs = [];
$stmt = $conn->prepare("SELECT * FROM activity_log ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Activity Logs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="form_area dashboard-container">
            <h2 class="title">Activity Logs</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>
            
            <form method="POST" action="view_logs.php" style="margin-bottom: 20px;">
                <input type="hidden" name="action" value="clear_logs">
                <button type="submit" class="btn delete-btn" onclick="return confirm('Are you sure you want to clear all logs?')">
                    <i class="fas fa-trash"></i> Clear All Logs
                </button>
            </form>
            
            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Activity</th>
                            <th>Description</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['id']) ?></td>
                            <td><?= htmlspecialchars($log['username']) ?> (ID: <?= htmlspecialchars($log['user_id']) ?>)</td>
                            <td><?= htmlspecialchars($log['activity_type']) ?></td>
                            <td><?= htmlspecialchars($log['description']) ?></td>
                            <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No activity logs found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <a href="dashboard.php" class="btn back-btn">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>