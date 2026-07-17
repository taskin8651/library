// ---- Mobile drawer (sidebar) with backdrop, Escape-to-close, and
//      click-outside-to-close — gives the hamburger menu a native app feel
//      instead of just sliding a panel over the page. ----
function toggleDrawer(forceClose) {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const burger = document.getElementById('drawerBurger');
    if (!sidebar) return;
    const shouldOpen = forceClose === true ? false : !sidebar.classList.contains('show');
    sidebar.classList.toggle('show', shouldOpen);
    if (backdrop) backdrop.classList.toggle('show', shouldOpen);
    if (burger) {
        burger.classList.toggle('open', shouldOpen);
        burger.setAttribute('aria-expanded', String(shouldOpen));
    }
    document.body.classList.toggle('drawer-open', shouldOpen);
}
(function () {
    const backdrop = document.getElementById('sidebarBackdrop');
    if (backdrop) backdrop.addEventListener('click', () => toggleDrawer(true));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') toggleDrawer(true);
    });
})();

// ---- Topbar hide-on-scroll-down / show-on-scroll-up (mobile only, via
//      the .topbar.nav-hidden class defined inside a max-width query) ----
(function () {
    const topbar = document.querySelector('.topbar');
    if (!topbar) return;
    let lastY = window.scrollY;
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            const y = window.scrollY;
            const drawerOpen = document.body.classList.contains('drawer-open');
            if (!drawerOpen && y > lastY && y > 80) {
                topbar.classList.add('nav-hidden');
            } else {
                topbar.classList.remove('nav-hidden');
            }
            lastY = y;
            ticking = false;
        });
    }, { passive: true });
})();

// Auto-dismiss toasts with fade-out
document.querySelectorAll('[data-autohide]').forEach(el => {
    setTimeout(() => {
        el.style.transition = 'opacity .35s ease, transform .35s ease';
        el.style.opacity = '0';
        el.style.transform = 'translateX(20px)';
        setTimeout(() => el.remove(), 400);
    }, 4500);
});

// Stagger fade-in for cards across every page (no per-view changes needed)
document.querySelectorAll('.stat-card, .table-card, .card-stat').forEach((el, i) => {
    el.classList.add('reveal-auto');
    el.style.animationDelay = (i * 0.06) + 's';
});

// Count-up animation for stat numbers like "1,234" or "₹1,234"
document.querySelectorAll('.stat-card h3').forEach(el => {
    const raw = el.textContent.trim();
    const match = raw.match(/^(\D*)([\d,]+)(\D*)$/);
    if (!match) return;
    const [, prefix, numStr, suffix] = match;
    const target = parseInt(numStr.replace(/,/g, ''), 10);
    if (isNaN(target)) return;
    const duration = 900;
    const start = performance.now();
    function tick(now) {
        const progress = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = prefix + Math.round(eased * target).toLocaleString('en-IN') + suffix;
        if (progress < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
});

// ---- Shared confirmation modal (replaces native confirm() dialogs) ----
// Any <form data-confirm="message"> is intercepted; submitting only happens
// after the user clicks "Yes, continue" in the modal.
(function () {
    const modalEl = document.getElementById('confirmModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;
    const modal = new bootstrap.Modal(modalEl);
    const bodyEl = document.getElementById('confirmModalBody');
    const confirmBtn = document.getElementById('confirmModalBtn');
    let pendingForm = null;

    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.dataset && form.dataset.confirm) {
            e.preventDefault();
            pendingForm = form;
            bodyEl.textContent = form.dataset.confirm;
            modal.show();
        }
    });

    confirmBtn.addEventListener('click', function () {
        if (pendingForm) {
            modal.hide();
            const formToSubmit = pendingForm;
            pendingForm = null;
            formToSubmit.submit();
        }
    });
})();

// ---- Button ripple effect ----
document.querySelectorAll('.btn').forEach(btn => btn.classList.add('ripple-host'));
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.ripple-host');
    if (!btn) return;
    const rect = btn.getBoundingClientRect();
    const ripple = document.createElement('span');
    const size = Math.max(rect.width, rect.height) * 1.4;
    ripple.className = 'ripple-span';
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
    ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
    btn.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);
});

// ---- Page fade transition ----
window.addEventListener('DOMContentLoaded', () => {
    requestAnimationFrame(() => document.body.classList.add('page-fade-loaded'));
});

// Pressing Back/Forward can restore this exact page from the browser's
// bfcache — including a `page-fade-out` class left over from the click that
// navigated away, which would otherwise leave the restored page stuck fully
// transparent. Reset the fade state whenever that happens.
window.addEventListener('pageshow', (e) => {
    if (e.persisted) {
        document.body.classList.remove('page-fade-out');
        document.body.classList.add('page-fade-loaded');
    }
});
document.addEventListener('click', function (e) {
    const a = e.target.closest('a[href]');
    if (!a) return;
    const url = new URL(a.href, window.location.href);
    const isSameOrigin = url.origin === window.location.origin;
    const isPlainClick = !e.ctrlKey && !e.metaKey && !e.shiftKey && e.button === 0;
    const opensNewTab = a.target === '_blank';
    const isHashLink = a.getAttribute('href').startsWith('#');
    const isDownload = a.hasAttribute('download');

    if (isSameOrigin && isPlainClick && !opensNewTab && !isHashLink && !isDownload) {
        e.preventDefault();
        document.body.classList.add('page-fade-out');
        setTimeout(() => { window.location.href = a.href; }, 160);
    }
});
