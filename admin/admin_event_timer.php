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
    <link rel="stylesheet" href="admin_styles.css">
    <style>
        .event-date-form {
            max-width: 500px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="datetime-local"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .save-btn {
            background: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .save-btn:hover {
            background: #45a049;
        }
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
                <div class="form-group">
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
        // Preview functionality
        function updatePreview() {
            const eventDateInput = document.getElementById('event_date');
            const eventDate = new Date(eventDateInput.value).getTime();
            const now = new Date().getTime();
            const distance = eventDate - now;

            if (distance < 0) {
                document.getElementById("countdown-preview").innerHTML = "EVENT IN PROGRESS!";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("preview-days").innerHTML = days;
            document.getElementById("preview-hours").innerHTML = hours;
            document.getElementById("preview-minutes").innerHTML = minutes;
            document.getElementById("preview-seconds").innerHTML = seconds;
        }

        // Update preview when date changes
        document.getElementById('event_date').addEventListener('change', updatePreview);
        updatePreview(); // Initial update
    </script>
</body>
</html>