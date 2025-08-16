<?php
require_once 'admin/db_connection.php';
require_once './includes/functions/functions.php';

// Fetch from DB or use default
$eventDate = getEventSetting('event_date', '2024-12-31 08:00:00');

// Make sure format is consistent for JS
$eventDateJS = date('Y-m-d\TH:i:s', strtotime($eventDate));
?>
<link rel="stylesheet" href="assets/css/custom_countdown.css">

<div class="event-header" style="height: 100px;">
  <div class="logo-container">
    <a href="index.php"><img src="assets/img/installersph.png" style="height: auto; width: 120px"></a>
  </div>
  <div id="countdown" class="d-flex justify-content-center gap-0.5 fs-5 align-items-center">
    <span><strong id="event-title">Time until the event starts</strong></span>
    <span><strong id="days">00</strong>d</span>
    <span><strong id="hours">00</strong>h</span>
    <span><strong id="minutes">00</strong>m</span>
    <span><strong id="seconds">00</strong>s</span>
  </div>
</div>

<script>
function updateCountdown() {
  const eventDate = new Date("<?= $eventDateJS ?>").getTime();
  const now = Date.now();
  const diff = eventDate - now;

  if (diff <= 0) {
    document.getElementById("countdown").innerHTML = `
      <div class="alert alert-success mt-3 text-center" role="alert">
        THE MARATHON HAS STARTED!
      </div>`;
    return;
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diff % (1000 * 60)) / 1000);

  document.getElementById("days").textContent = days.toString().padStart(2, '0');
  document.getElementById("hours").textContent = hours.toString().padStart(2, '0');
  document.getElementById("minutes").textContent = minutes.toString().padStart(2, '0');
  document.getElementById("seconds").textContent = seconds.toString().padStart(2, '0');
}

window.addEventListener('DOMContentLoaded', () => {
  updateCountdown();
  setInterval(updateCountdown, 1000);
});
</script>
