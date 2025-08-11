<?php


require_once 'admin/db_connection.php';
require_once 'includes/functions/Search.php';
$search_result = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search'])) {
    $participant_id = intval($_POST['participant_id']);
    $search_result = get_participant_by_id($participant_id);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Landing Page</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/search.css">
  <?php require_once 'includes/countdown.php';?>
</head>
<body class="bdy d-flex flex-column min-vh-100">


  <!-- Main Content -->
  <main class="flex-grow-1 d-flex flex-column align-items-center justify-content-center py-5 px-3 " ">
   
    <class="container" style="max-width: 480px;">
    <img src="assets/img/background.png" alt="" id="background-image"  style="width: 300px; height: auto;  ">

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
          <a href="index.php" class="btn btn-success w-100 ms-md-2">Register</a>
        </div>
      </form>
      
      <!-- Search Result -->
      <?php if ($search_result): ?>
        <div class="card shadow-sm mb-3">
          <div class="card-body">
            <h5 class="card-title">Participant Found</h5>
            <p class="card-text"><strong>Status:</strong> <?= htmlspecialchars($search_result['payment_status']) ?></p>
            <p class="card-text"><strong>RUN-ID:</strong> <?= htmlspecialchars($search_result['id']) ?></p>
            <p class="card-text"><strong>Name:</strong> <?= htmlspecialchars($search_result['full_name']) ?></p>
            <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($search_result['email']) ?></p>
            <p class="card-text"><strong>Distance:</strong> <?= htmlspecialchars($search_result['distance']) ?></p>
            <p class="card-text"><strong>Registered On:</strong> <?= htmlspecialchars($search_result['registration_date']) ?></p>
          </div>
        </div>
        
    
      <?php elseif ($_SERVER["REQUEST_METHOD"] === "POST"): ?>
        <div class="alert alert-danger text-center">No participant found with that ID.</div>
      <?php endif; ?>
 
      <!-- Sponsor -->
      <div class="text-center text-muted mt-4">
        Sponsored by <br><strong>INSTALLERSPH IT SOLUTION</strong>
      </div>
    </div>

  </main>

  <!-- Footer -->
  <?php require_once 'includes/footer.php'; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
