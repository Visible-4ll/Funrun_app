<?php
session_start();
require_once '../db_connection.php';
require_once './includes/functions/Search.php';

$search_result = null;    


// Search by participant ID
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
    $participant_id = intval($_POST['participant_id']);
    $search_result = get_participant_by_id($participant_id);

    // Fetch staff name if paid
    if ($search_result && !empty($search_result['received_by'])) {
        $stmtStaff = $pdo->prepare("SELECT full_name FROM admin_users WHERE id = ?");
        $stmtStaff->execute([$search_result['received_by']]);
        $staffName = $stmtStaff->fetchColumn();
        $search_result['staff_name'] = $staffName ?: $search_result['received_by'];
    }
}

// If redirected after payment, auto-load the participant data
if (isset($_GET['status'], $_GET['txn']) && $_GET['status'] === 'paid') {
    $stmt = $pdo->prepare("SELECT * FROM participants WHERE transaction_number = ?");
    $stmt->execute([$_GET['txn']]);
    $search_result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($search_result && !empty($search_result['received_by'])) {
        $stmtStaff = $pdo->prepare("SELECT full_name FROM admin_users WHERE id = ?");
        $stmtStaff->execute([$search_result['received_by']]);
        $staffName = $stmtStaff->fetchColumn();
        $search_result['staff_name'] = $staffName ?: $search_result['received_by'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Landing Page</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/search.css">
  <?php require_once './staff_header.php' ?>
</head>
<body class="bdy d-flex flex-column min-vh-100">

<main class="flex-grow-1 d-flex flex-column align-items-center justify-content-center py-5 px-3">
  <div class="container" style="max-width: 480px;">

    <?php if (isset($_GET['status']) && $_GET['status'] === 'paid'): ?>
      <div class="alert alert-success text-center">
        Payment marked as <strong>Paid</strong> successfully.
      </div>
    <?php endif; ?>

    <img src="assets/img/background.png" alt="" id="background-image" style="width: 300px; height: auto;">

    <!-- Search Form -->
    <form method="POST" class="mb-4">
      <div class="mb-3">
        <input 
          type="number" 
          name="participant_id" 
          class="form-control form-control-lg" 
          placeholder="Enter Participant ID..." 
          required
        >
      </div>
      <div class="d-grid gap-2 d-md-flex justify-content-md-between">
        <button type="submit" name="search" class="btn btn-primary w-100 me-md-2">Search</button>
      </div>
    </form>
    
    <!-- Search Result -->
    <?php if ($search_result): ?>
      <div class="card shadow-sm mb-3">
        <div class="card-body">
          <h5 class="card-title">Participant Found</h5>
          <p class="card-text"><strong>Status:</strong> 
            <span class="badge bg-<?= $search_result['payment_status'] === 'Paid' ? 'success' : 'warning' ?>">
              <?= htmlspecialchars($search_result['payment_status']) ?>
            </span>
          </p>
          <p class="card-text"><strong>RUN-ID:</strong> <?= htmlspecialchars($search_result['id']) ?></p>
          <p class="card-text"><strong>Name:</strong> <?= htmlspecialchars($search_result['full_name']) ?></p>
          <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($search_result['email']) ?></p>
          <p class="card-text"><strong>Distance:</strong> <?= htmlspecialchars($search_result['distance']) ?></p>
          <p class="card-text"><strong>Registered On:</strong> <?= htmlspecialchars($search_result['registration_date']) ?></p>
          <p class="card-text"><strong>Transaction #:</strong> <?= htmlspecialchars($search_result['transaction_number']) ?></p>

          <?php if (!empty($search_result['staff_name'])): ?>
            <p class="card-text"><strong>Processed By:</strong> <?= htmlspecialchars($search_result['staff_name']) ?></p>
          <?php endif; ?>

          <?php if ($search_result['payment_status'] !== 'Paid'): ?>
            <div class="text-center my-4">
              <a href="includes/pay-now.php?txn=<?= urlencode($search_result['transaction_number']) ?>" 
                 class="btn btn-primary">
                 Pay Now
              </a>
            </div>
          <?php endif; ?>

        </div>
      </div>
    <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
      <div class="alert alert-danger text-center">No participant found with that ID.</div>
    <?php endif; ?>

    <div class="text-center text-muted mt-4">
      Sponsored by <br><strong>INSTALLERSPH IT SOLUTION</strong>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
