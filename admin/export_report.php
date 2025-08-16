<?php
require_once 'admin_auth.php';

$type = $_GET['type'] ?? 'full';

// Get report data based on type
try {
    if ($type === 'full') {
        // Full report with all participants
        $stmt = $pdo->query("SELECT * FROM participants ORDER BY registration_date DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $filename = 'full_report_' . date('Y-m-d') . '.csv';
    } else {
        // Summary report
        $reportData = [];
        
        // Total participants
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM participants");
        $reportData['Total Participants'] = $stmt->fetch()['total'];
        
        // By distance
        $stmt = $pdo->query("SELECT distance, COUNT(*) as count FROM participants GROUP BY distance");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $reportData[$row['distance']] = $row['count'];
        }
        
        // By payment method
        $stmt = $pdo->query("SELECT payment_method, COUNT(*) as count FROM participants GROUP BY payment_method");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $reportData[$row['payment_method']] = $row['count'];
        }
        
        // By gender
        $stmt = $pdo->query("SELECT gender, COUNT(*) as count FROM participants GROUP BY gender");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $reportData[$row['gender']] = $row['count'];
        }
        
        $data = $reportData;
        $filename = 'summary_report_' . date('Y-m-d') . '.csv';
    }
    
    // Log the export
    logAdminActivity("Exported $type report");
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    if ($type === 'full') {
        // Header row for full report
        fputcsv($output, array_keys($data[0]));
        
        // Data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    } else {
        // Summary report
        fputcsv($output, ['Category', 'Count']);
        foreach ($data as $key => $value) {
            fputcsv($output, [$key, $value]);
        }
    }
    
    fclose($output);
    exit;
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>