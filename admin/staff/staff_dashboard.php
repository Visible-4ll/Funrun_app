<?php
session_start();

// Authentication check
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'staff') {
    header('Location: staff_login.php'); // or staff_login.php if separate
    exit;
}

// Database connection
require_once './staff_db.php';

// Get statistics data
try {
    // Total participants by distance
    $stmt = $pdo->query("SELECT distance, COUNT(*) as count FROM participants GROUP BY distance");
    $distanceStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Participants by payment method
    $stmt = $pdo->query("SELECT payment_method, COUNT(*) as count FROM participants GROUP BY payment_method");
    $paymentStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Handle error gracefully
    $distanceStats = [];
    $paymentStats = [];
    error_log("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/staff_dashboard.css">
    <title>Staff Dashboard</title>
   
</head>
<body>
    <?php include 'staff_header.php'; ?>
    
    <div class="dashboard">
        <h1>Welcome to Staff Dashboard</h1>
        <p class="welcome-message">You are logged in as: <?= htmlspecialchars($_SESSION['staff_username'] ?? 'Staff') ?></p>
        
        <class="report-section">
            <h2>Registration Statistics</h2>
            
            <div class="report-cards">
    <div class="report-card">
        <h3>Total Participants</h3>
        <p class="report-number">
            <?= !empty($distanceStats) ? number_format(array_sum(array_column($distanceStats, 'count'))) : '0' ?>
        </p>
    </div>
    
    <div class="report-card">
        <h3>Most Popular Distance</h3>
        <?php if (!empty($distanceStats)): 
            usort($distanceStats, function($a, $b) { return $b['count'] - $a['count']; }); ?>
            <p class="report-number"><?= htmlspecialchars($distanceStats[0]['distance']) ?></p>
            <p class="report-detail"><?= number_format($distanceStats[0]['count']) ?> participants</p>
        <?php else: ?>
            <p class="report-detail">No data available</p>
        <?php endif; ?>
    </div>
    
    <div class="report-card">
        <h3>Most Used Payment</h3>
        <?php if (!empty($paymentStats)): 
            usort($paymentStats, function($a, $b) { return $b['count'] - $a['count']; }); ?>
            <p class="report-number"><?= htmlspecialchars($paymentStats[0]['payment_method']) ?></p>
            <p class="report-detail"><?= number_format($paymentStats[0]['count']) ?> participants</p>
        <?php else: ?>
            <p class="report-detail">No data available</p>
        <?php endif; ?>
    </div>
</div>
                
            
        </div>
    </div>
</body>
</html>