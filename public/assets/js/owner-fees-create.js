document.querySelector('[name=payment_mode]').addEventListener('change', function() {
    document.getElementById('upi_ref_div').style.display = this.value === 'upi' ? 'block' : 'none';
});
