function updateSizeDescriptions() {
    document.querySelectorAll('.size-description').forEach(desc => {
        desc.style.display = 'none';
    });

    const selected = document.querySelector('input[name="shirt_size"]:checked');
    if (selected) {
        const parent = selected.closest('.shirt-option');
        const desc = parent.querySelector('.size-description');
        if (desc) {
            desc.style.display = 'block';
        }
    }
}

// Initialize on load
window.addEventListener('DOMContentLoaded', updateSizeDescriptions);

// Listen for changes
document.querySelectorAll('input[name="shirt_size"]').forEach(input => {
    input.addEventListener('change', updateSizeDescriptions);
});