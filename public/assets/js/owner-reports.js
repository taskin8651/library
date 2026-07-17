// Brief loading feedback on the export actions — these trigger a file
// download rather than a page navigation, so without this a click can feel
// like it did nothing for a moment.
(function () {
    function setLoading(el, label) {
        if (!el || el.dataset.originalHtml) return;
        el.dataset.originalHtml = el.innerHTML;
        el.classList.add('is-loading');
        el.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + label;
        setTimeout(function () {
            el.innerHTML = el.dataset.originalHtml;
            el.classList.remove('is-loading');
            delete el.dataset.originalHtml;
        }, 1400);
    }

    document.querySelectorAll('.report-export-form').forEach(function (form) {
        form.addEventListener('submit', function () {
            setLoading(form.querySelector('.report-export-btn'), 'Preparing...');
        });
    });

    document.querySelectorAll('a.report-export-btn').forEach(function (a) {
        a.addEventListener('click', function () {
            setLoading(a, 'Preparing...');
        });
    });
})();
