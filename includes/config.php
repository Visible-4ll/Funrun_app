<?php
session_start();

// Detect if we're on local or live server
$isLocal = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

// Define base URL
if ($isLocal) {
    // Local IP so phones on same Wi-Fi can access it
    $base_url = "http://localhost/funrunapplication/"; // <-- Replace with your actual local IP
} else {
    // Production (live)
    $base_url = "https://localhost/funrunapplication/"; // <-- Replace with your live URL
}
?>
// Update this to your actual base URL

');


?>