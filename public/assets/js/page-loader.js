// Shared full-page loading overlay: covers the blank-white gap on first
// paint, shows again briefly on back/forward-cache restore (browser Back
// button), and on outgoing link clicks / form submits so navigation always
// feels continuous instead of a jarring flash.
(function () {
    var loader = document.getElementById('global-page-loader');
    if (!loader) return;

    function hideLoader() { loader.classList.add('hidden'); }
    function showLoader() { loader.classList.remove('hidden'); }

    window.addEventListener('load', function () {
        setTimeout(hideLoader, 250);
    });

    // Pages restored from bfcache (e.g. pressing Back) never fire `load`
    // again — without this the very first page load's hide would be the
    // only thing that ever ran, which is fine normally, but on some
    // browsers a restored page can briefly repaint mid-scroll-position-
    // restore, so a quick reassuring flash here smooths that over.
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            showLoader();
            setTimeout(hideLoader, 200);
        } else {
            hideLoader();
        }
    });

    document.addEventListener('click', function (e) {
        var a = e.target.closest && e.target.closest('a[href]');
        if (!a) return;
        var href = a.getAttribute('href') || '';
        if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0
            || href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0
            || a.target === '_blank' || a.hasAttribute('download')
            || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        showLoader();
    });

    document.addEventListener('submit', function (e) {
        if (e.target && e.target.tagName === 'FORM') showLoader();
    });
})();
