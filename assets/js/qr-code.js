
document.addEventListener('DOMContentLoaded', function () {
    const regId = "RUN-<?= $_SESSION['registration_id'] ?? uniqid() ?>";
    const canvas = document.getElementById('qr-canvas');
    const downloadBtn = document.getElementById('download-qr-btn');
    const qrImage = document.getElementById('main-qr-code');

    // Download QR code on button click
    downloadBtn.addEventListener('click', function () {

        const link = document.createElement('a');
        link.download = 'event-qr-' + regId + '.png';
        
    
        link.href = qrImage.src;
        link.click();
    });
});