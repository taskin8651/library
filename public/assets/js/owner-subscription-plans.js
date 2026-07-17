let currentSubscriptionId = null;

function startUpiPayment(planId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    fetch('/owner/subscription/order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ plan_id: planId })
    })
    .then(r => r.json())
    .then(data => {
        if (!data.subscription_id) {
            alert('Could not start payment. Please try again.');
            return;
        }
        currentSubscriptionId = data.subscription_id;

        document.getElementById('upiModalTitle').textContent = 'Pay for ' + data.plan_name + ' Plan';
        document.getElementById('upiQrBox').innerHTML = data.upi_qr;
        document.getElementById('upiAmount').textContent = '₹' + Number(data.amount).toLocaleString('en-IN');
        document.getElementById('upiIdText').textContent = data.upi_id;
        document.getElementById('upiUtrInput').value = '';
        document.getElementById('upiFormMsg').textContent = '';
        document.getElementById('upiSubmitBtn').disabled = false;

        const modal = new bootstrap.Modal(document.getElementById('upiPaymentModal'));
        modal.show();
    })
    .catch(() => alert('Error creating order. Try again.'));
}

document.addEventListener('DOMContentLoaded', function () {
    const copyBtn = document.getElementById('upiCopyBtn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            const text = document.getElementById('upiIdText').textContent;
            navigator.clipboard.writeText(text).then(() => {
                copyBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Copied';
                setTimeout(() => { copyBtn.innerHTML = '<i class="bi bi-clipboard me-1"></i>Copy'; }, 1500);
            });
        });
    }

    const utrForm = document.getElementById('upiUtrForm');
    if (utrForm) {
        utrForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!currentSubscriptionId) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const utr = document.getElementById('upiUtrInput').value.trim();
            const msg = document.getElementById('upiFormMsg');
            const btn = document.getElementById('upiSubmitBtn');

            if (!utr) return;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

            fetch('/owner/subscription/submit-utr', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ subscription_id: currentSubscriptionId, utr })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    msg.className = 'small mb-2 text-success';
                    msg.textContent = data.message;
                    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Submitted';
                    setTimeout(() => window.location.reload(), 1800);
                } else {
                    msg.className = 'small mb-2 text-danger';
                    msg.textContent = data.message || 'Something went wrong. Please try again.';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>I\'ve Paid — Submit for Verification';
                }
            })
            .catch(() => {
                msg.className = 'small mb-2 text-danger';
                msg.textContent = 'Network error. Please try again.';
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>I\'ve Paid — Submit for Verification';
            });
        });
    }
});
