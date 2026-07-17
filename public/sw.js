const CACHE_NAME = 'liberpx-shell-v1';
const SHELL_ASSETS = [
    '/manifest.json',
    '/images/icon-192.png',
    '/images/icon-512.png',
    '/assets/css/student-dashboard.css',
    '/assets/css/auth-login.css',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(SHELL_ASSETS)).catch(() => {})
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') return;

    const url = new URL(req.url);

    // HTML pages: always go to the network first so attendance/plan data stays fresh.
    // Only fall back to a cached shell asset when fully offline.
    if (req.mode === 'navigate') {
        event.respondWith(
            fetch(req).catch(() => caches.match(req).then((r) => r || caches.match('/manifest.json')))
        );
        return;
    }

    // Static assets (css/js/images/fonts): cache-first for speed.
    if (['style', 'script', 'image', 'font'].includes(req.destination)) {
        event.respondWith(
            caches.match(req).then((cached) => {
                if (cached) return cached;
                return fetch(req).then((res) => {
                    if (res.ok && url.origin === self.location.origin) {
                        const clone = res.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(req, clone));
                    }
                    return res;
                }).catch(() => cached);
            })
        );
    }
});
