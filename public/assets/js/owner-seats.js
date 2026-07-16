// Tap/click feedback pulse + opens the shift-wise detail modal for a seat.
document.querySelectorAll('.seat-desk').forEach(seat => {
    seat.addEventListener('click', function () {
        this.classList.remove('seat-tapped');
        void this.offsetWidth;
        this.classList.add('seat-tapped');
        if (window.SEAT_DATA) openSeatModal(this.dataset.seatId);
    });
});

function esc(str) {
    return String(str ?? '').replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]));
}

function openSeatModal(seatId) {
    const d = window.SEAT_DATA[seatId];
    if (!d) return;

    document.getElementById('seatModalTitle').textContent = 'Seat ' + d.seat_number;
    document.getElementById('seatModalSubtitle').textContent =
        (d.row_label ? 'Row ' + d.row_label + ' · ' : '') + d.type.charAt(0).toUpperCase() + d.type.slice(1) + ' seat';

    const banner   = document.getElementById('seatModalBanner');
    const slotWrap = document.getElementById('seatModalSlots');
    const footer   = document.getElementById('seatModalFooter');

    banner.className = 'seat-modal-banner d-none';
    banner.innerHTML = '';
    slotWrap.innerHTML = '';
    slotWrap.classList.remove('d-none');
    footer.innerHTML = '';

    const sellUrl = (shiftId) => window.SEAT_ROUTES.memberCreate + '?seat_id=' + d.id + (shiftId ? '&shift_id=' + shiftId : '');
    const memberUrl = (id) => window.SEAT_ROUTES.memberShow.replace('__ID__', id);

    if (d.blocked) {
        const reasonLabel = { inactive: 'Inactive', maintenance: 'Under Maintenance', reserved: 'Reserved' }[d.blocked_reason] || 'Unavailable';
        banner.className = 'seat-modal-banner seat-banner-' + (d.blocked_reason || 'inactive');
        banner.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>This seat is currently <strong>' + esc(reasonLabel) + '</strong> and cannot be sold right now.';
        slotWrap.classList.add('d-none');
    } else if (d.full_day_taken) {
        banner.className = 'seat-modal-banner seat-banner-full';
        banner.innerHTML = '<i class="bi bi-person-fill-lock me-2"></i>Booked full-day by <strong>' + esc(d.full_day_taken.name) + '</strong> until ' + esc(d.full_day_taken.until || '—') + '. ' +
            '<a class="ss-link d-inline" href="' + memberUrl(d.full_day_taken.member_id) + '">View Member</a>';
        slotWrap.classList.add('d-none');
    } else if (!window.SHIFT_LIST || window.SHIFT_LIST.length === 0) {
        banner.className = 'seat-modal-banner seat-banner-free';
        banner.innerHTML = '<i class="bi bi-info-circle me-2"></i>No shifts configured — this seat sells on a full-day basis.';
        slotWrap.innerHTML = '<a class="btn btn-primary" href="' + sellUrl() + '"><i class="bi bi-cart-plus me-1"></i>Sell This Seat</a>';
    } else {
        window.SHIFT_LIST.forEach(shift => {
            const occ = d.shifts[shift.id];
            const row = document.createElement('div');
            row.className = 'seat-slot-row ' + (occ ? 'is-booked' : 'is-free');
            row.innerHTML =
                '<div class="ss-info">' +
                    '<div class="ss-name">' + esc(shift.name) + '</div>' +
                    '<div class="ss-time"><i class="bi bi-clock me-1"></i>' + esc(shift.time) + '<span class="ss-price">₹' + esc(shift.price) + '/mo</span></div>' +
                '</div>' +
                '<div class="ss-action">' + (occ
                    ? ('<div class="ss-occupant"><span class="ss-pill ss-pill-booked">Sold</span>' +
                       '<div class="ss-occ-name">' + esc(occ.name) + '</div>' +
                       '<div class="ss-occ-until">until ' + esc(occ.until || '—') + '</div>' +
                       '<a class="ss-link" href="' + memberUrl(occ.member_id) + '">View Member</a></div>')
                    : ('<span class="ss-pill ss-pill-free">Available</span>' +
                       '<a class="btn btn-sm btn-primary" href="' + sellUrl(shift.id) + '"><i class="bi bi-cart-plus me-1"></i>Sell</a>')
                ) + '</div>';
            slotWrap.appendChild(row);
        });
    }

    // Management actions footer (always available regardless of booking state)
    const csrf = window.CSRF_TOKEN;
    const toggleUrl  = window.SEAT_ROUTES.toggle.replace('__ID__', d.id);
    const statusUrl  = window.SEAT_ROUTES.status.replace('__ID__', d.id);
    const destroyUrl = window.SEAT_ROUTES.destroy.replace('__ID__', d.id);

    function actionForm(action, method, hiddenFields, label, cls, icon, confirmMsg, disabled) {
        let hidden = '<input type="hidden" name="_token" value="' + csrf + '">';
        if (method !== 'POST') hidden += '<input type="hidden" name="_method" value="' + method + '">';
        hidden += hiddenFields || '';
        return '<form method="POST" action="' + action + '" class="d-inline seat-modal-form"' + (confirmMsg ? ' data-confirm="' + esc(confirmMsg) + '"' : '') + '>' +
            hidden +
            '<button type="submit" class="btn btn-sm ' + cls + '"' + (disabled ? ' disabled' : '') + '><i class="bi ' + icon + ' me-1"></i>' + label + '</button>' +
        '</form>';
    }

    const hasAnyBooking = d.booked_count > 0 || !!d.full_day_taken;

    let footerHtml = '';
    if (d.status !== 'available') {
        footerHtml += actionForm(statusUrl, 'POST', '<input type="hidden" name="status" value="available">', 'Mark Available', 'btn-outline-success', 'bi-check-circle');
    }
    if (!hasAnyBooking) {
        if (d.status !== 'reserved') {
            footerHtml += actionForm(statusUrl, 'POST', '<input type="hidden" name="status" value="reserved">', 'Reserve', 'btn-outline-secondary', 'bi-bookmark');
        }
        if (d.status !== 'maintenance') {
            footerHtml += actionForm(statusUrl, 'POST', '<input type="hidden" name="status" value="maintenance">', 'Maintenance', 'btn-outline-warning', 'bi-tools');
        }
    }
    footerHtml += actionForm(toggleUrl, 'POST', '', d.is_active ? 'Deactivate' : 'Activate', 'btn-outline-dark', 'bi-power');
    footerHtml += actionForm(
        destroyUrl, 'DELETE', '', 'Delete Seat', 'btn-outline-danger', 'bi-trash',
        'Delete seat ' + d.seat_number + '? This cannot be undone.',
        hasAnyBooking
    );
    footer.innerHTML = footerHtml;
}

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
