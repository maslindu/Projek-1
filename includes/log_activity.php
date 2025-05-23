<?php
function log_activity($user_id, $username, $activity_type, $description) {
    global $conn;
    
    // Pastikan koneksi database tersedia
    if (!isset($conn)) {
        require_once 'config.php';
    }
    
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, activity_type, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $username, $activity_type, $description);
    $stmt->execute();
    $stmt->close();
}
?>