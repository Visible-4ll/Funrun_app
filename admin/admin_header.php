<div class="admin-header">
  <img src="assets/img/installersph.png" alt="Logo" class="logo">
  <button class="burger" onclick="toggleMenu()">☰</button>

  <nav class="admin-nav" id="adminNav">
    <ul>
      <li><a href="admin_dashboard.php">Dashboard</a></li>
      <li><a href="admin_participants.php">Participants</a></li>
      <li><a href="admin_reports.php">Reports</a></li>
      <li><a href="admin_settings.php">Settings</a></li>
      <li><a href="admin_event_timer.php">Event Timer</a></li>
      <li><a href="admin_logout.php">Logout (<?= htmlspecialchars($_SESSION['admin_username']) ?>)</a></li>
    </ul>
  </nav>
</div>

<link rel="stylesheet" href="assets/css/admin_header.css">
<script src="assets/js/togglemenu.js"></script>
