<?php
session_start();

// Redirect to login if NOT authenticated as staff
if (!isset($_SESSION['logged_in'], $_SESSION['role']) || $_SESSION['role'] !== 'staff') {
    header('Location: ./staff_login.php');
    exit;
}

// Database connection
require_once './staff_db.php';

// Log staff activity
function logstaffActivity($action) {
    global $pdo;
    
    // Check if staff_id is set in session
    if (!isset($_SESSION['staff_id'])) {
        error_log('Attempt to log activity without staff_id');
        return; // Skip logging if no staff_id
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO staff_logs (staff_id, action, ip_address, user_agent) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['staff_id'],
            $action,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);
    } catch (PDOException $e) {
        error_log('Failed to log staff activity: ' . $e->getMessage());
    }
}
?>
