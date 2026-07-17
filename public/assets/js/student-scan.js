const librarySlug = document.body.dataset.librarySlug;
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const scanHint = document.getElementById('scanHint');

let scanner = null;
let busy = false;

function extractSlug(rawText) {
    try {
        const url = new URL(rawText);
        const parts = url.pathname.split('/').filter(Boolean);
        const idx = parts.indexOf('checkin');
        if (idx !== -1 && parts[idx + 1]) return parts[idx + 1];
    } catch (e) {
        // Not a URL — treat the raw scanned text as the slug itself.
    }
    return rawText.trim();
}

function showResult(success, icon, title, message) {
    const overlay = document.getElementById('result-overlay');
    overlay.className = 'result-overlay show ' + (success ? 'success' : 'error');
    document.getElementById('result-icon').textContent = icon;
    document.getElementById('result-title').textContent = title;
    document.getElementById('result-message').textContent = message;
    document.getElementById('result-spinner').classList.add('d-none');
}

function hideResult() {
    document.getElementById('result-overlay').classList.remove('show');
}

function onScanSuccess(decodedText) {
    if (busy) return;
    busy = true;

    const slug = extractSlug(decodedText);
    if (scanner) scanner.pause(true);

    fetch('/student/scan/checkin', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ slug }),
    })
        .then((r) => r.json())
        .then((data) => {
            if (data.success) {
                const isIn = data.action === 'checkin';
                const msg = isIn
                    ? `Seat: ${data.seat || 'No seat'} · ${data.days_left} days left`
                    : `Have a great day, ${data.member}!`;
                showResult(true, isIn ? '✅' : '👋', isIn ? `Welcome, ${data.member}!` : 'Checked Out', msg);
                if ('vibrate' in navigator) navigator.vibrate(200);
                setTimeout(() => {
                    if (scanner) scanner.stop().catch(() => {});
                    window.location.href = '/student/dashboard';
                }, 2200);
            } else {
                showResult(false, '❌', 'Not Checked In', data.message);
                if ('vibrate' in navigator) navigator.vibrate([80, 60, 80]);
                setTimeout(() => {
                    hideResult();
                    busy = false;
                    if (scanner) scanner.resume();
                }, 2400);
            }
        })
        .catch(() => {
            showResult(false, '⚠️', 'Network Error', 'Could not reach the server. Try again.');
            setTimeout(() => {
                hideResult();
                busy = false;
                if (scanner) scanner.resume();
            }, 2400);
        });
}

if (typeof Html5Qrcode === 'undefined') {
    scanHint.textContent = 'Camera scanner failed to load. Use classic check-in below.';
} else {
    scanner = new Html5Qrcode('qr-reader');
    scanner
        .start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: 260 },
            onScanSuccess,
            () => {} // per-frame "no QR found" noise — ignore
        )
        .catch(() => {
            scanHint.innerHTML = '<i class="bi bi-camera-video-off me-1"></i>Camera access denied or unavailable. Use classic check-in below.';
        });
}
