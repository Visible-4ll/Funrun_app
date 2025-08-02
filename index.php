<?php
session_start();
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Running Event Registration</title><header class="event-header">
</header>
<?php require_once 'includes/countdown.php'; ?>
<?php require_once 'includes/registration.php'; ?>
<body>
</body>