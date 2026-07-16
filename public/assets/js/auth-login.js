function togglePass() {
    const input = document.getElementById('passwordInput');
    const icon = document.getElementById('toggleIcon');
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    icon.className = isPass ? 'bi bi-eye-slash' : 'bi bi-eye';
}
document.getElementById('loginForm').addEventListener('submit', function () {
    document.getElementById('loginBtnText').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing in...';
});
