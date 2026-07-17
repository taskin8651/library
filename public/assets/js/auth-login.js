function togglePasswordField(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    icon.className = isPass ? 'bi bi-eye-slash' : 'bi bi-eye';
}

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
