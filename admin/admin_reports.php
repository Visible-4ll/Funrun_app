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

    $stmt = $pdo->query("SELECT SUM(price) as total_revenue, AVG(price) as avg_price FROM participants");
    $priceStats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Price breakdown by distance
    $stmt = $pdo->query("SELECT distance, SUM(price) as total FROM participants GROUP BY distance");
    $priceByDistance = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="assets/css/admin_reports.css">
</head>
 
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

            <div class="chart-row">
                <div class="chart-container">
                    <canvas id="priceByDistanceChart"></canvas>
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
       
        const priceCtx = document.getElementById('priceByDistanceChart').getContext('2d');
new Chart(priceCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($priceByDistance, 'distance')) ?>,
        datasets: [{
            label: 'Total Revenue (₱)',
            data: <?= json_encode(array_map('floatval', array_column($priceByDistance, 'total'))) ?>,
            backgroundColor: 'rgba(255, 159, 64, 0.7)',
            borderColor: 'rgba(255, 159, 64, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Revenue by Distance'
            }
        }
    }
});
        
    </script>
</body>
</html>