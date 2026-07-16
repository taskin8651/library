// Animate the days-left progress ring in on load
window.addEventListener('DOMContentLoaded', () => {
    requestAnimationFrame(() => {
        const ring = document.getElementById('daysRing');
        if (ring) ring.style.strokeDashoffset = ring.dataset.offset;
    });

    // Count-up numbers
    document.querySelectorAll('.count-up').forEach(el => {
        const target = parseInt(el.dataset.target || el.textContent, 10) || 0;
        const duration = 900;
        const start = performance.now();
        function tick(now) {
            const progress = Math.min(1, (now - start) / duration);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(eased * target).toLocaleString('en-IN');
            if (progress < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    });
});

function copyUid(el) {
    const uid = el.dataset.uid;
    navigator.clipboard?.writeText(uid).then(() => {
        const icon = el.querySelector('i');
        const original = icon.className;
        icon.className = 'bi bi-check2';
        setTimeout(() => icon.className = original, 1200);
    });
}
