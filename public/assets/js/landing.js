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

    /* ── CURSOR GLOW (desktop only) ───────────────────────── */
    var glow = document.getElementById('cursor-glow');
    if (glow && window.innerWidth > 768) {
        document.addEventListener('mousemove', function (e) {
            glow.style.left = e.clientX + 'px';
            glow.style.top  = e.clientY + 'px';
        }, { passive: true });
    }

    /* ── SCROLL REVEAL ────────────────────────────────────── */
    var revealEls = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduceMotion || !('IntersectionObserver' in window) || !revealEls.length) {
        /* Reduced-motion users / old browsers: just show everything immediately. */
        revealEls.forEach(function (el) { el.classList.add('visible'); });
    } else {
        var revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -40px 0px' });
        revealEls.forEach(function (el) { revealObserver.observe(el); });

        /* Safety net: on some mobile browsers (in-app WebViews, fast anchor
           jumps, layout-not-settled-yet on load) the observer can miss an
           element that's already on screen. Force-reveal anything still
           hidden a couple of seconds after load so content never gets
           permanently stuck invisible. */
        window.addEventListener('load', function () {
            setTimeout(function () {
                revealEls.forEach(function (el) {
                    if (!el.classList.contains('visible')) el.classList.add('visible');
                });
            }, 2500);
        });
    }

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

    /* ── COUNTER ANIMATION ────────────────────────────────── */
    function animateCounter(el, target) {
        var start     = null;
        var duration  = 1800;

        function step(timestamp) {
            if (!start) start = timestamp;
            var progress = Math.min((timestamp - start) / duration, 1);
            /* Ease out quad */
            var ease = 1 - (1 - progress) * (1 - progress);
            var val  = Math.floor(ease * target);
            el.textContent = val.toLocaleString('en-IN') + '+';
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = target.toLocaleString('en-IN') + '+';
            }
        }
        requestAnimationFrame(step);
    }

    var counterEls = document.querySelectorAll('.num[data-target]');
    if ('IntersectionObserver' in window && counterEls.length) {
        var counterObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var el     = entry.target;
                    var target = parseInt(el.dataset.target, 10);
                    if (target) animateCounter(el, target);
                    counterObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });
        counterEls.forEach(function (el) { counterObserver.observe(el); });
    }

    /* ── LIVE CHECK-IN SIMULATION ─────────────────────────── */
    var names  = ['Priya Sharma','Amit Kumar','Ravi Singh','Sneha Gupta','Rohit Jha','Pooja Devi','Vikash Kumar','Neha Singh','Deepak Yadav','Anita Kumari'];
    var seats  = ['A1','A2','A3','B1','B2','C3','D4','B5','A4','C1'];
    var shifts = ['Morning Shift','Evening Shift','Full Day'];
    var ni     = 0;

    function simulateLive() {
        var liveBox = document.querySelector('.live-box');
        if (!liveBox) return;

        var name  = names[ni % names.length];
        var seat  = seats[ni % seats.length];
        var shift = shifts[ni % shifts.length];
        ni++;

        /* Fade out */
        liveBox.style.opacity   = '0';
        liveBox.style.transform = 'translateY(8px)';

        setTimeout(function () {
            var avatarEl = liveBox.querySelector('.live-avatar');
            var nameEl   = liveBox.querySelector('.live-name');
            var metaEl   = liveBox.querySelector('.live-meta');
            if (avatarEl) avatarEl.textContent = name.charAt(0);
            if (nameEl) nameEl.textContent = name + ' checked in ✓';
            if (metaEl) metaEl.textContent = 'Seat ' + seat + '  •  ' + shift + '  •  just now';

            /* Fade in */
            liveBox.style.transition = 'opacity .4s ease, transform .4s ease';
            liveBox.style.opacity    = '1';
            liveBox.style.transform  = 'translateY(0)';
        }, 300);
    }

    setInterval(simulateLive, 3500);

});
