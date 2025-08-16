<?php
session_start();
require_once '../../db_connection.php'; // Adjust path if needed

if (!isset($_GET['txn']) || empty($_GET['txn'])) {
    die("Invalid transaction.");
}

$transactionNumber = $_GET['txn'];

try {
    // Update payment status
    $stmt = $pdo->prepare("UPDATE participants SET payment_status = 'Paid' WHERE transaction_number = ?");
    $stmt->execute([$transactionNumber]);


    header("Location: ../../staff/Search.php?status=paid&txn=" . urlencode($transactionNumber));
    exit;
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
