// Shared tap/click ripple effect for buttons and button-styled links.
// Pages under layouts/app.blade.php already have this built into
// app-layout.js — only include this file on standalone pages that don't
// extend that layout, so the effect never gets bound twice.
(function () {
    function addRipple(el) {
        if (el.dataset.rippleBound) return;
        el.dataset.rippleBound = '1';
        el.classList.add('ripple-host');
        el.addEventListener('click', function (e) {
            const rect = el.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height) * 1.4;
            const span = document.createElement('span');
            span.className = 'ripple-span';
            span.style.width = span.style.height = size + 'px';
            span.style.left = (e.clientX - rect.left - size / 2) + 'px';
            span.style.top = (e.clientY - rect.top - size / 2) + 'px';
            el.appendChild(span);
            setTimeout(() => span.remove(), 600);
        });
    }
    document.querySelectorAll('a[class*="btn"], button').forEach(addRipple);
})();
