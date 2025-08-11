<?php
session_start();
require_once 'db_connection.php';

// Admin authentication check
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventDate = $_POST['event_date'] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT INTO event_settings (setting_name, setting_value) 
                              VALUES ('event_date', ?)
                              ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$eventDate, $eventDate]);

        $success = "Event date updated successfully!";
    } catch (PDOException $e) {
        $error = "Error updating event date: " . $e->getMessage();
    }
}

// Get current event date
$currentEventDate = getEventSetting('event_date', '2024-12-31 08:00:00');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event Timer</title>
    <link rel="stylesheet" href="assets/css/admin_styles.css">
    <link rel="stylesheet" href="assets/css/admin_event_timer.css">
    <style>
       
    </style>
</head>
<body>
    <?php include 'admin_header.php'; ?>

    <div class="admin-container">
        <h1>Edit Event Countdown Timer</h1>

        <?php if (isset($success)): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="event-date-form">
            <form method="post">
                <div class="form-group">
                    <label for="event_date">New Event Date & Time:</label>
                    <input type="datetime-local" id="event_date" name="event_date"
                           value="<?= date('Y-m-d\TH:i', strtotime($currentEventDate)) ?>" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="save-btn">Save Changes</button>
                    <a href="admin_dashboard.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>

        <div class="current-timer-preview">
            <h2>Current Timer Preview</h2>
            <div id="countdown-preview">
                <span id="preview-days"></span>D
                <span id="preview-hours"></span>H
                <span id="preview-minutes"></span>M
                <span id="preview-seconds"></span>S
            </div>
        </div>
    </div>

    <script>
        function updatePreview() {
            const eventDateInput = document.getElementById('event_date');
            const preview = document.getElementById('countdown-preview');

            if (!eventDateInput.value) return;

            const eventDate = new Date(eventDateInput.value).getTime();
            const now = new Date().getTime();
            const distance = eventDate - now;

            if (distance <= 0) {
                preview.innerHTML = "EVENT IN PROGRESS!";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("preview-days").textContent = days;
            document.getElementById("preview-hours").textContent = hours;
            document.getElementById("preview-minutes").textContent = minutes;
            document.getElementById("preview-seconds").textContent = seconds;
        }

        document.getElementById('event_date').addEventListener('change', updatePreview);
        setInterval(updatePreview, 1000); // Auto-refresh preview every second
        updatePreview(); // Initial update
    </script>
</body>
</html>
