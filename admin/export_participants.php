<?php
require_once 'admin_auth.php';

$format = $_GET['format'] ?? 'csv';
$search = $_GET['search'] ?? '';
$distance = $_GET['distance'] ?? '';
$payment = $_GET['payment'] ?? '';

// Build query
$query = "SELECT * FROM participants";
$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(full_name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($distance)) {
    $conditions[] = "distance = ?";
    $params[] = $distance;
}

if (!empty($payment)) {
    $conditions[] = "payment_method = ?";
    $params[] = $payment;
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log the export
    logAdminActivity("Exported participants to $format");
    
    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="participants_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Header row
        fputcsv($output, array_keys($participants[0]));
        
        // Data rows
        foreach ($participants as $participant) {
            fputcsv($output, $participant);
        }
        
        fclose($output);
    } elseif ($format === 'excel') {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="participants_' . date('Y-m-d') . '.xls"');
        
        echo '<table border="1">';
        echo '<tr>';
        foreach (array_keys($participants[0]) as $header) {
            echo '<th>' . htmlspecialchars($header) . '</th>';
        }
        echo '</tr>';
        
        foreach ($participants as $participant) {
            echo '<tr>';
            foreach ($participant as $value) {
                echo '<td>' . htmlspecialchars($value) . '</td>';
            }
            echo '</tr>';
        }
        
        echo '</table>';
    }
    
    exit;
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>