function togglePasswordField(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    icon.className = isPass ? 'bi bi-eye-slash' : 'bi bi-eye';
}

const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
        const btn = document.getElementById('regBtn');
        if (btn.disabled) {
            e.preventDefault();
            return;
        }
        btn.disabled = true;
        document.getElementById('regBtnText').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating your library...';
    });
}
