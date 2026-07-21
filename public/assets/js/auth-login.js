function togglePasswordField(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    icon.className = isPass ? 'bi bi-eye-slash' : 'bi bi-eye';
}

// The installed PWA (manifest start_url points here on launch) is the
// student-only app. Flag the login submit so the server can reject
// owner/staff/admin credentials when signing in from that context —
// display-mode:standalone / navigator.standalone covers Android + iOS.
(function () {
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches
        || window.navigator.standalone === true;
    if (!isStandalone) return;

    const flag = document.getElementById('pwaFlag');
    if (flag) flag.value = '1';

    const notice = document.getElementById('pwaOnlyNotice');
    if (notice) notice.classList.remove('d-none');
})();

document.getElementById('loginForm').addEventListener('submit', function (e) {
    const btn = document.getElementById('loginBtn');
    if (btn.disabled) {
        // Guards against a double-tap firing two submits before the first
        // navigation completes.
        e.preventDefault();
        return;
    }
    btn.disabled = true;
    document.getElementById('loginBtnText').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing in...';
});
