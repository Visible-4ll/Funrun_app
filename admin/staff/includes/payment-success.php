<?php
require_once '../admin/db_connection.php';

if (!isset($_GET['txn'])) {
    die("Invalid request.");
}

$transactionNumber = $_GET['txn'];

// Get participant info
$stmt = $pdo->prepare("SELECT * FROM participants WHERE transaction_number = ?");
$stmt->execute([$transactionNumber]);
$participant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$participant) {
    die("Participant not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Success</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container text-center mt-5">
    <div class="card shadow p-4">
        <h1 class="text-success">✅ Payment Successful!</h1>
        <p>Thank you, <strong><?= htmlspecialchars($participant['name']) ?></strong>.</p>
        <p>Your payment for transaction <strong><?= htmlspecialchars($participant['transaction_number']) ?></strong> has been recorded as:</p>
        <h3 class="text-primary"><?= htmlspecialchars($participant['payment_status']) ?></h3>
        <a href="your-dashboard.php" class="btn btn-primary mt-3">Go Back</a>
    </div>
</div>
</body>
</html>
