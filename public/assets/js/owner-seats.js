// Tap/click feedback pulse on seat desks (skip clicks on the actions menu)
document.querySelectorAll('.seat-desk').forEach(seat => {
    seat.addEventListener('click', function (e) {
        if (e.target.closest('.seat-desk-menu')) return;
        this.classList.remove('seat-tapped');
        void this.offsetWidth;
        this.classList.add('seat-tapped');
    });
});

const seatSearch = document.getElementById('seatSearch');
if (seatSearch) {
    seatSearch.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        document.querySelectorAll('.seat-row-group').forEach(group => {
            let anyVisible = false;
            group.querySelectorAll('.seat-desk').forEach(seat => {
                const match = !q || seat.dataset.seatNumber.includes(q);
                seat.classList.toggle('seat-hidden', !match);
                if (match) anyVisible = true;
            });
            group.classList.toggle('seat-row-hidden', !anyVisible);
        });
    });
}
