(function () {
    // ---- Search + status filter (pure client-side — the shift list is
    // never paginated, it's fully rendered in the page already) ----
    var searchInput = document.getElementById('shiftSearchInput');
    var statusFilter = document.getElementById('shiftStatusFilter');
    var rows = document.querySelectorAll('.shift-tr');
    var visibleCount = document.getElementById('shiftVisibleCount');
    var noMatch = document.getElementById('shiftNoMatch');
    var tableWrap = document.querySelector('#shiftsTable')?.closest('.table-responsive');

    function applyFilters() {
        var q = (searchInput?.value || '').trim().toLowerCase();
        var status = statusFilter?.value || '';
        var shown = 0;

        rows.forEach(function (row) {
            var matchesSearch = !q || row.dataset.name.indexOf(q) !== -1;
            var matchesStatus = !status || row.dataset.status === status;
            var show = matchesSearch && matchesStatus;
            row.classList.toggle('d-none', !show);
            if (show) shown++;
        });

        if (visibleCount) visibleCount.textContent = '(' + shown + ')';
        if (noMatch) noMatch.classList.toggle('d-none', shown !== 0);
        if (tableWrap) tableWrap.classList.toggle('d-none', shown === 0);
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);

    // ---- CSV export (built from the rows already on the page) ----
    var exportBtn = document.getElementById('shiftExportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', function () {
            var header = ['Shift', 'Start Time', 'End Time', 'Price', 'Status', 'Active Members'];
            var lines = [header.join(',')];

            rows.forEach(function (row) {
                if (row.classList.contains('d-none')) return; // respect active filters
                var cells = row.querySelectorAll('td');
                var name = cells[0].querySelector('.shift-name-cell span:last-child').textContent.trim();
                var timeText = cells[1].querySelector('.shift-time-badge').textContent.trim().replace(/\s+/g, ' ');
                var times = timeText.split(' - ');
                var price = cells[2].textContent.trim().replace(/[₹\s]/g, '');
                var status = cells[3].textContent.trim();
                var members = cells[4].textContent.trim();

                var fields = [name, times[0] || '', times[1] || '', price, status, members].map(function (f) {
                    return '"' + String(f).replace(/"/g, '""') + '"';
                });
                lines.push(fields.join(','));
            });

            var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'shifts.csv';
            document.body.appendChild(a);
            a.click();
            a.remove();
        });
    }

    // ---- Loading state on add/edit/toggle/delete submissions ----
    function setSubmitLoading(form) {
        var btn = form.querySelector('button[type="submit"]');
        if (!btn || btn.disabled) return;
        btn.dataset.originalHtml = btn.innerHTML;
        btn.disabled = true;
        var label = form.id === 'addShiftForm' ? 'Adding...' : '';
        btn.innerHTML = '<span class="spinner-border spinner-border-sm' + (label ? ' me-2' : '') + '"></span>' + label;
    }

    var addForm = document.getElementById('addShiftForm');
    if (addForm) addForm.addEventListener('submit', function () { setSubmitLoading(addForm); });

    document.querySelectorAll('.shift-action-form').forEach(function (form) {
        form.addEventListener('submit', function () { setSubmitLoading(form); });
    });

    document.querySelectorAll('.modal form').forEach(function (form) {
        form.addEventListener('submit', function () { setSubmitLoading(form); });
    });
})();
