<?php
require_once 'admin_auth.php';

// Get report data
try {
    // Total participants by distance
    $stmt = $pdo->query("SELECT distance, COUNT(*) as count FROM participants GROUP BY distance");
    $distanceStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Participants by payment method
    $stmt = $pdo->query("SELECT payment_method, COUNT(*) as count FROM participants GROUP BY payment_method");
    $paymentStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Daily registration trend
    $stmt = $pdo->query("SELECT DATE(registration_date) as date, COUNT(*) as count 
                         FROM participants 
                         GROUP BY DATE(registration_date) 
                         ORDER BY date");
    $dailyTrend = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Gender distribution
    $stmt = $pdo->query("SELECT gender, COUNT(*) as count FROM participants GROUP BY gender");
    $genderStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Shirt size distribution
    $stmt = $pdo->query("SELECT shirt_size, COUNT(*) as count FROM participants GROUP BY shirt_size");
    $shirtStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Log report view
logAdminActivity("Viewed reports");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Running Event</title>
    <link rel="stylesheet" href="admin_styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
/* Admin Reports CSS */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f7fa;
    color: #333;
    line-height: 1.6;
}

.admin-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

h1 {
    color: #2c3e50;
    margin-bottom: 30px;
    padding-bottom: 10px;
    border-bottom: 2px solid #ecf0f1;
}

h2 {
    color: #3498db;
    margin: 25px 0 15px;
}

.report-section {
    margin-bottom: 40px;
}

.report-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.report-card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    border-left: 4px solid #3498db;
}

.report-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.report-card h3 {
    color: #2c3e50;
    margin-top: 0;
    font-size: 1.1rem;
}

.report-number {
    font-size: 2rem;
    font-weight: bold;
    color: #3498db;
    margin: 10px 0 5px;
}

.report-detail {
    color: #7f8c8d;
    font-size: 0.9rem;
    margin: 0;
}

.chart-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 30px;
    margin-bottom: 30px;
}

.chart-container {
    position: relative;
    height: 300px;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.report-actions {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 40px;
}

.export-btn {
    display: inline-block;
    padding: 12px 25px;
    background-color: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    text-align: center;
}

.export-btn:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .report-cards {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
    
    .chart-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .admin-container {
        padding: 15px;
        margin: 10px;
    }
    
    .report-cards {
        grid-template-columns: 1fr;
    }
    
    .chart-container {
        height: 250px;
    }
    
    .report-actions {
        flex-direction: column;
        gap: 10px;
    }
    
    .export-btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 1.5rem;
    }
    
    h2 {
        font-size: 1.3rem;
    }
    
    .report-card {
        padding: 15px;
    }
    
    .report-number {
        font-size: 1.5rem;
    }
    
    .chart-container {
        height: 220px;
        padding: 10px;
    }
}

/* Print styles */
@media print {
    body {
        background: none;
        color: #000;
    }
    
    .admin-container {
        box-shadow: none;
        padding: 0;
        max-width: 100%;
    }
    
    .report-card {
        box-shadow: none;
        border: 1px solid #ddd;
        page-break-inside: avoid;
    }
    
    .chart-container {
        page-break-inside: avoid;
        height: auto;
        min-height: 300px;
    }
    
    .report-actions {
        display: none;
    }
}
</style>
<body>
    <?php include 'admin_header.php'; ?>
    
    <div class="admin-container">
        <h1>Event Reports</h1>
        
        <div class="report-section">
            <h2>Registration Statistics</h2>
            
            <div class="report-cards">
                <div class="report-card">
                    <h3>Total Participants</h3>
                    <?php
                    $total = array_sum(array_column($distanceStats, 'count'));
                    ?>
                    <p class="report-number"><?= number_format($total) ?></p>
                </div>
                
                <div class="report-card">
                    <h3>Most Popular Distance</h3>
                    <?php
                    usort($distanceStats, function($a, $b) {
                        return $b['count'] - $a['count'];
                    });
                    ?>
                    <p class="report-number"><?= htmlspecialchars($distanceStats[0]['distance']) ?></p>
                    <p class="report-detail"><?= number_format($distanceStats[0]['count']) ?> participants</p>
                </div>
                
                <div class="report-card">
                    <h3>Most Used Payment</h3>
                    <?php
                    usort($paymentStats, function($a, $b) {
                        return $b['count'] - $a['count'];
                    });
                    ?>
                    <p class="report-number"><?= htmlspecialchars($paymentStats[0]['payment_method']) ?></p>
                    <p class="report-detail"><?= number_format($paymentStats[0]['count']) ?> participants</p>
                </div>
            </div>
        </div>
        
        <div class="report-section">
            <h2>Charts</h2>
            
            <div class="chart-row">
                <div class="chart-container">
                    <canvas id="distanceChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
            
            <div class="chart-row">
                <div class="chart-container">
                    <canvas id="dailyTrendChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
            
            <div class="chart-row">
                <div class="chart-container">
                    <canvas id="shirtSizeChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="report-actions">
            <a href="export_report.php?type=full" class="export-btn">Export Full Report</a>
            <a href="export_report.php?type=summary" class="export-btn">Export Summary</a>
        </div>
    </div>

    <script>
        // Distance Chart
        const distanceCtx = document.getElementById('distanceChart').getContext('2d');
        new Chart(distanceCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($distanceStats, 'distance')) ?>,
                datasets: [{
                    label: 'Participants',
                    data: <?= json_encode(array_column($distanceStats, 'count')) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Participants by Distance'
                    }
                }
            }
        });

        // Payment Chart
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        new Chart(paymentCtx, {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_column($paymentStats, 'payment_method')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($paymentStats, 'count')) ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Payment Methods'
                    }
                }
            }
        });

        // Daily Trend Chart
        const dailyCtx = document.getElementById('dailyTrendChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($dailyTrend, 'date')) ?>,
                datasets: [{
                    label: 'Registrations',
                    data: <?= json_encode(array_column($dailyTrend, 'count')) ?>,
                    fill: false,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Daily Registration Trend'
                    }
                }
            }
        });

        // Gender Chart
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($genderStats, 'gender')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($genderStats, 'count')) ?>,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(255, 206, 86, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Gender Distribution'
                    }
                }
            }
        });

        // Shirt Size Chart
        const shirtCtx = document.getElementById('shirtSizeChart').getContext('2d');
        new Chart(shirtCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($shirtStats, 'shirt_size')) ?>,
                datasets: [{
                    label: 'Participants',
                    data: <?= json_encode(array_column($shirtStats, 'count')) ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.7)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Shirt Size Distribution'
                    }
                }
            }
        });
    </script>
</body>
</html>