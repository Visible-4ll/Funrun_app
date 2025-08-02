<?php
session_start();

// Authentication check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

// Database connection
require_once 'admin_db.php';

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
    <title>Admin Dashboard</title>
    <style>
        /* Admin Dashboard Styles */
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --danger-color: #e74c3c;
            --success-color: #2ecc71;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --text-color: #333;
            --border-radius: 6px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: var(--text-color);
            line-height: 1.6;
        }

        .dashboard {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        h1 {
            color: var(--dark-color);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-color);
        }

        h2 {
            color: var(--primary-color);
            margin: 25px 0 15px;
        }

        .welcome-message {
            font-size: 1.1em;
            color: var(--dark-color);
            background: var(--light-color);
            padding: 10px 15px;
            border-radius: var(--border-radius);
            display: inline-block;
        }

        /* Report Cards Styling */
        .report-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .report-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            border-top: 4px solid var(--primary-color);
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .report-card h3 {
            color: var(--dark-color);
            margin-top: 0;
            font-size: 1.1rem;
        }

        .report-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin: 10px 0;
        }

        .report-detail {
            color: var(--text-color);
            font-size: 0.9rem;
            margin: 5px 0 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard {
                padding: 20px;
                margin: 15px;
            }
            
            .report-cards {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .dashboard {
                padding: 15px;
            }
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dashboard {
            animation: fadeIn 0.5s ease-out;
        }

        .report-card {
            animation: fadeIn 0.5s ease-out;
            animation-fill-mode: both;
        }

        .report-card:nth-child(1) { animation-delay: 0.1s; }
        .report-card:nth-child(2) { animation-delay: 0.2s; }
        .report-card:nth-child(3) { animation-delay: 0.3s; }
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>
    
    <div class="dashboard">
        <h1>Welcome to Admin Dashboard</h1>
        <p class="welcome-message">You are logged in as: <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></p>
        
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