/* ============================================================
   Softlix - Landing Page JavaScript
   File: public/assets/js/landing.js
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    /* ── PAGE LOADER ──────────────────────────────────────── */
    var pageLoader = document.getElementById('page-loader');
    window.addEventListener('load', function () {
        setTimeout(function () {
            if (pageLoader) pageLoader.classList.add('hidden');
        }, 800);
    });

    /* Restored from bfcache (Back/Forward button) — `load` won't fire again,
       so give a quick reassuring flash instead of an instant, jarring cut. */
    window.addEventListener('pageshow', function (e) {
        if (!pageLoader) return;
        if (e.persisted) {
            pageLoader.classList.remove('hidden');
            setTimeout(function () { pageLoader.classList.add('hidden'); }, 300);
        } else {
            pageLoader.classList.add('hidden');
        }
    });

    /* ── NAVBAR SCROLL (glass state + hide-on-scroll-down) ──── */
    var navbar = document.getElementById('navbar');
    if (navbar) {
        var lastScrollY = window.scrollY;
        var ticking = false;
        window.addEventListener('scroll', function () {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(function () {
                var y = window.scrollY;
                navbar.classList.toggle('scrolled', y > 40);
                // Only auto-hide on the way down, past the header's own height,
                // and never while the mobile menu is open.
                var menuOpen = document.body.classList.contains('nav-open');
                if (!menuOpen && y > lastScrollY && y > 120) {
                    navbar.classList.add('nav-hidden');
                } else {
                    navbar.classList.remove('nav-hidden');
                }
                lastScrollY = y;
                ticking = false;
            });
        }, { passive: true });
    }

    /* ── MOBILE MENU (hamburger <-> X, backdrop, ESC, outside-click,
       auto-close-on-link-click, scroll-lock) — single controller, bound
       once on DOMContentLoaded so there's no risk of duplicate listeners. */
    (function () {
        var burger = document.getElementById('navBurger');
        var menu = document.getElementById('navMenu');
        var backdrop = document.getElementById('navBackdrop');
        if (!burger || !menu || !backdrop) return;

        var isOpen = false;

        function openMenu() {
            if (isOpen) return;
            isOpen = true;
            menu.classList.add('show');
            backdrop.classList.add('show');
            burger.classList.add('open');
            burger.setAttribute('aria-expanded', 'true');
            document.body.classList.add('nav-open');
        }
        function closeMenu() {
            if (!isOpen) return;
            isOpen = false;
            menu.classList.remove('show');
            backdrop.classList.remove('show');
            burger.classList.remove('open');
            burger.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('nav-open');
        }

        burger.addEventListener('click', function () {
            isOpen ? closeMenu() : openMenu();
        });
        backdrop.addEventListener('click', closeMenu);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeMenu();
        });
        menu.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', closeMenu);
        });
        // If the viewport grows into the desktop layout while the drawer is
        // open, reset state so it doesn't reappear mid-transition later.
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 992) closeMenu();
        });
    })();

    /* ── FAQ ACCORDION ────────────────────────────────────── */
    document.querySelectorAll('.faq-q').forEach(function (q) {
        q.addEventListener('click', function () {
            var item   = q.parentElement;
            var isOpen = item.classList.contains('open');
            /* Close all */
            document.querySelectorAll('.faq-item').forEach(function (i) {
                i.classList.remove('open');
            });
            /* Open clicked (if it was closed) */
            if (!isOpen) item.classList.add('open');
        });
    });

    /* ── SMOOTH SCROLL ────────────────────────────────────── */
    /* (Closing the mobile menu on click is handled by the menu controller
       above, which binds to every link inside #navMenu — no need to
       duplicate that here.) */
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var target = document.querySelector(a.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* ── COUNTER (static — shows final numbers immediately, no count-up) ── */
    document.querySelectorAll('.num[data-target]').forEach(function (el) {
        var target = parseInt(el.dataset.target, 10);
        if (target) el.textContent = target.toLocaleString('en-IN') + '+';
    });

});
