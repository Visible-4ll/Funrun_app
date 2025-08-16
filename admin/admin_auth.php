<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

// Database connection
require_once 'admin_db.php';

// Log admin activity
function logAdminActivity($action) {
    global $pdo;
    
    // Check if admin_id is set in session
    if (!isset($_SESSION['admin_id'])) {
        error_log('Attempt to log activity without admin_id');
        return; // Skip logging if no admin_id
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, action, ip_address, user_agent) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['admin_id'],
            $action,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);
    } catch (PDOException $e) {
        error_log('Failed to log admin activity: ' . $e->getMessage());
    }
}
?>