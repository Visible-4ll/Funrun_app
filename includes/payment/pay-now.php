<?php
session_start();
require_once '../../admin/db_connection.php';
$_SESSION['payment_status'] = 'paid'; 

$feedback = '';
$participantName = '';

if (isset($_GET['txn'])) {
    $transactionNumber = $_GET['txn'];

    // Lookup participant
    $stmt = $pdo->prepare("SELECT * FROM participants WHERE transaction_number = ?");
    $stmt->execute([$transactionNumber]);
    $participant = $stmt->fetch();

    if ($participant) {
        // Update payment status to 'Paid'
        $update = $pdo->prepare("UPDATE participants SET payment_status = 'Paid' WHERE transaction_number = ?");
        $update->execute([$transactionNumber]);

        $participantName = htmlspecialchars($participant['full_name']);
        $feedback = '<div class="alert alert-success" role="alert">
                        <h4 class="alert-heading">Payment Successful!</h4>
                        <p>Thank you, <strong>' . $participantName . '</strong>. Your payment has been recorded.</p>
                    </div>';
    } else {
        $feedback = '<div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">Invalid Transaction</h4>
                        <p>No participant found with this transaction number.</p>
                    </div>';
    }
} else {
    $feedback = '<div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">Error</h4>
                    <p>No transaction number provided.</p>
                </div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/pay-now.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-9 col-lg-8 col-xl-6">
                <div class="card shadow-lg p-4 p-md-5 text-center">
                    <h1 class="mb-4 display-6 display-md-5">Marathon Registration Payment</h1>
                    <?= $feedback ?>
                    <a href="../../index.php" class="btn btn-primary btn-lg mt-4 px-5 py-3 w-100 w-md-auto">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
    }

    .card {
        border-radius: 20px;
        background-color: #fff;
        border: none;
    }

    h1 {
        font-size: 2rem;
    }

    .btn-lg {
        font-size: 1.2rem;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
    }

    @media (min-width: 576px) {
        h1 {
            font-size: 2.5rem;
        }

        .btn-lg {
            font-size: 1.3rem;
            padding: 1rem 2rem;
        }
    }

    @media (min-width: 768px) {
        h1 {
            font-size: 3rem;
        }
    }
</style>
