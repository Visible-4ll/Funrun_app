
<!-- Countdown Styles -->
<link rel="stylesheet" href="assets/css/custom_countdown.css">

<!-- Countdown HTML Block -->
<div class="event-header " style="height: 100px;">
  <div class="logo-container ">
    <a href="http://10.0.16.46/funrunapplication-main/Search.php"><img src="assets/img/installersph.png" style="height: auto; width: 120px"></img></a>
  </div>

  <div id="countdown" class="d-flex justify-content-center gap-0.5 fs-5 align-items-center ">
    <span><strong id="event-title">TIme until the event Started</strong></span>
    <span><strong id="days">00</strong>d</span>
    <span><strong id="hours">00</strong>h</span>
    <span><strong id="minutes">00</strong>m</span>
    <span><strong id="seconds">00</strong>s</span>
  </div>
</div>

<!-- Countdown Script -->
<script>
function updateCountdown() {
  const eventDate = new Date("<?= getEventSetting('event_date', '2024-12-31 08:00:00') ?>").getTime();
  const now = new Date().getTime();
  const diff = eventDate - now;

  if (diff < 0) {
    document.getElementById("countdown").innerHTML = `
      <div class="alert alert-success mt-3 text-center" role="alert margin-top: 20px;">
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

updateCountdown();
setInterval(updateCountdown, 1000);


//sticky header effect
window.addEventListener('scroll', function () {
    const header = document.querySelector('.event-header');
    if (window.scrollY > 10) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  });
</script>