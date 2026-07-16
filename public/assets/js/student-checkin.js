const librarySlug = document.body.dataset.librarySlug;
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function processCheckIn() {
    const input = document.getElementById('uid-input');
    const uid = input.value.trim().toUpperCase();
    if (!uid || uid.length < 4) {
        triggerShake();
        showResult(false, '⚠️', 'Invalid UID', 'Please enter your 6-digit UID');
        return;
    }

    const btn = document.getElementById('checkinBtn');
    const btnText = document.getElementById('checkinBtnText');
    btn.disabled = true;
    btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Checking...';

    fetch('/checkin/process', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ uid, library_slug: librarySlug })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const isIn = data.action === 'checkin';
            let msg = isIn
                ? `Seat: ${data.seat || 'No seat'} | ${data.days_left} days left`
                : `Have a great day, ${data.member}!`;
            showResult(true, isIn ? '✅' : '👋', isIn ? `Welcome, ${data.member}!` : 'Checked Out', msg);
            if ('vibrate' in navigator) navigator.vibrate(200);
        } else {
            triggerShake();
            showResult(false, '❌', 'Access Denied', data.message);
        }
        input.value = '';
        setTimeout(() => hideResult(), 4500);
    })
    .catch(() => {
        triggerShake();
        showResult(false, '⚠️', 'Error', 'Network error. Try again.');
    })
    .finally(() => {
        btn.disabled = false;
        btnText.innerHTML = '<i class="bi bi-door-open me-2"></i>Check In / Out';
    });
}

function triggerShake() {
    const input = document.getElementById('uid-input');
    input.classList.remove('shake-error');
    void input.offsetWidth;
    input.classList.add('shake-error');
}

function showResult(success, icon, title, msg) {
    const box = document.getElementById('result-box');
    box.className = 'result-box show ' + (success ? 'success' : 'error');
    document.getElementById('result-icon').textContent = icon;
    document.getElementById('result-title').textContent = title;
    document.getElementById('result-message').textContent = msg;
}

function hideResult() {
    const box = document.getElementById('result-box');
    box.classList.remove('show');
}

// Enter key support
document.getElementById('uid-input').addEventListener('keypress', e => {
    if (e.key === 'Enter') processCheckIn();
});
