// Highlight selected plan
document.querySelectorAll('input[name="plan_id"]').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
        radio.closest('.plan-card').classList.add('selected');
    });
});
// Init
document.querySelector('input[name="plan_id"]:checked')?.closest('.plan-card')?.classList.add('selected');

document.getElementById('registerForm').addEventListener('submit', function () {
    document.getElementById('regBtnText').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating your library...';
});
