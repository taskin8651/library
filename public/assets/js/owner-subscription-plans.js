function startPayment(planId, planName, amount) {
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
        const options = {
            key: data.rzp_key,
            amount: data.amount,
            currency: data.currency,
            name: 'LibraryCRM',
            description: planName + ' Plan - Monthly',
            order_id: data.order_id,
            prefill: {
                name: data.library,
                email: data.email,
                contact: data.phone
            },
            theme: { color: '#667eea' },
            handler: function(response) {
                // Verify payment
                fetch('/owner/subscription/verify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(response)
                })
                .then(r => r.redirected ? window.location.href = r.url : r.json())
                .then(d => { if(d && d.error) alert('Payment failed: ' + d.error); });
            }
        };
        const rzp = new Razorpay(options);
        rzp.open();
    })
    .catch(err => alert('Error creating order. Try again.'));
}
