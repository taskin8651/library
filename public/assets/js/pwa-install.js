// Registers the service worker and wires up any [data-pwa-install-btn]
// button on the page to trigger the "Add to Home Screen" install flow.
(function () {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }

    const isStandalone = window.matchMedia('(display-mode: standalone)').matches
        || window.navigator.standalone === true;

    const buttons = Array.from(document.querySelectorAll('[data-pwa-install-btn]'));
    if (buttons.length === 0 || isStandalone) return;

    const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent) && !window.MSStream;
    let deferredPrompt = null;

    function showButtons() {
        buttons.forEach((btn) => btn.classList.remove('d-none'));
    }

    if (isIos) {
        // iOS Safari has no beforeinstallprompt — guide the user manually.
        showButtons();
        buttons.forEach((btn) => btn.addEventListener('click', function () {
            alert('To install: tap the Share icon in Safari, then "Add to Home Screen".');
        }));
    } else {
        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredPrompt = e;
            showButtons();
        });

        buttons.forEach((btn) => btn.addEventListener('click', async function () {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            await deferredPrompt.userChoice;
            deferredPrompt = null;
            buttons.forEach((b) => b.classList.add('d-none'));
        }));

        window.addEventListener('appinstalled', function () {
            buttons.forEach((btn) => btn.classList.add('d-none'));
        });
    }
})();
