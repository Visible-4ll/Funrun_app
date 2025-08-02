<?php
session_start();
require_once 'includes/db_connection.php'; // if needed

if (isset($_GET['transaction'])) {
    $txn = $_GET['transaction'];

    // Simulate success: update session and/or database
    $_SESSION['payment_status'] = 'Paid';

    // Optional: update your database
    $stmt = $conn->prepare("UPDATE registrations SET status = 'paid' WHERE transaction_number = ?");
    $stmt->bind_param("s", $txn);
    $stmt->execute();

    echo "<h1>Payment Successful!</h1>";
    echo "<p>Transaction No: $txn</p>";
    echo "<a href='index.php?step=5' class='btn btn-success'>Back to Registration</a>";
} else {
    echo "<p>Invalid transaction number.</p>";
}
?>
