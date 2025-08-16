<?php
session_start();
require_once 'db_connection.php';
require_once '../includes/functions/functions.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventDate = $_POST['event_date'] ?? '';
    // Normalize to full MySQL datetime
    $formattedDate = date('Y-m-d H:i:s', strtotime($eventDate));
    $stmt = $pdo->prepare("
        INSERT INTO event_settings (setting_name, setting_value) 
        VALUES ('event_date', ?) 
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
    ");
    $stmt->execute([$formattedDate]);
    $success = "Event date updated successfully!";
}

$currentEventDate = getEventSetting('event_date', '2024-12-31 08:00:00');
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Event Timer</title>
<link rel="stylesheet" href="assets/css/admin_styles.css">
</head>
<body>
<?php include 'admin_header.php'; ?>
<div class="admin-container">
    <h1>Edit Event Countdown Timer</h1>
    <?php if (isset($success)): ?><div class="success-message"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <form method="post">
        <label for="event_date">New Event Date & Time:</label>
        <input type="datetime-local" id="event_date" name="event_date"
               value="<?= date('Y-m-d\TH:i', strtotime($currentEventDate)) ?>" required>
        <button type="submit">Save Changes</button>
    </form>

    <h2>Live Preview</h2>
    <div id="countdown-preview">
        <span id="preview-days">00</span>d
        <span id="preview-hours">00</span>h
        <span id="preview-minutes">00</span>m
        <span id="preview-seconds">00</span>s
    </div>
</div>

<script>
function updatePreview() {
    const eventDateInput = document.getElementById('event_date');
    if (!eventDateInput.value) return;

    const eventDate = new Date(document.getElementById('event_date').value);
    const now = new Date().getTime();
    const diff = eventDate - now;

    if (diff <= 0) {
        document.getElementById("countdown-preview").innerHTML = "EVENT IN PROGRESS!";
        return;
    }

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    document.getElementById("preview-days").textContent = days.toString().padStart(2, '0');
    document.getElementById("preview-hours").textContent = hours.toString().padStart(2, '0');
    document.getElementById("preview-minutes").textContent = minutes.toString().padStart(2, '0');
    document.getElementById("preview-seconds").textContent = seconds.toString().padStart(2, '0');
}

window.addEventListener('DOMContentLoaded', function () {
    updatePreview();
    document.getElementById('event_date').addEventListener('input', updatePreview);
    setInterval(updatePreview, 1000);
});
</script>
</body>
</html>
