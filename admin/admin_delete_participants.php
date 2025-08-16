<?php
require_once 'admin_auth.php';

if (!isset($_GET['id'])) {
    header('Location: admin_participants.php');
    exit;
}

$id = (int)$_GET['id'];

try {
    // First get participant info for logging
    $stmt = $pdo->prepare("SELECT full_name FROM participants WHERE id = ?");
    $stmt->execute([$id]);
    $participant = $stmt->fetch();
    
    if (!$participant) {
        header('Location: admin_participants.php');
        exit;
    }
    
    // Delete the participant
    $stmt = $pdo->prepare("DELETE FROM participants WHERE id = ?");
    $stmt->execute([$id]);
    
    // Log the deletion
    logAdminActivity("Deleted participant: " . $participant['full_name'] . " (ID: $id)");
    
    $_SESSION['success_message'] = 'Participant deleted successfully';
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Error deleting participant: ' . $e->getMessage();
}

header('Location: admin_participants.php');
exit;
?>