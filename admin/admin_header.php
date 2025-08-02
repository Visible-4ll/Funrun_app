

<div class="admin-header">
  <h1>Running Event</h1>
  <button class="burger" onclick="toggleMenu()">☰</button>
  <script src="assets/js/togglemenu.js"></script>
  <link rel="stylesheet" href="assets/css/admin_header.css">
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

