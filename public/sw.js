/*
 * Service worker for gto.com.ly (works in browsers and inside the Android app's WebView).
 *
 *  - No internet: pages show /offline.html instead of the browser/app error screen.
 *  - Graduate ID card: the last copy the graduate opened online is kept on their device, so the
 *    card (with its QR code) can be shown at the job fair without internet.
 *  - Static files (CSS, JS, fonts, images, CDN libraries) are cached so those pages render offline.
 *
 * Private data (the ID card page) is removed when the user logs out ("clear-private" message).
 * Bump VERSION to force browsers to drop old caches after a change to this file.
 */
const VERSION = 'v1';
const SHELL_CACHE = 'gto-shell-' + VERSION;
const STATIC_CACHE = 'gto-static-' + VERSION;
const PRIVATE_CACHE = 'gto-private-' + VERSION;

const OFFLINE_URL = '/offline.html';
const ID_CARD_PATH = '/graduate/id-card';
const SHELL_FILES = [OFFLINE_URL, '/images/office_logo_white.png'];

const STATIC_HOSTS = ['cdn.jsdelivr.net', 'cdnjs.cloudflare.com', 'fonts.googleapis.com', 'fonts.gstatic.com', 'fonts.bunny.net'];
const STATIC_PATH = /^\/(build|css|js|images|fonts|assets|vendor)\//;

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(SHELL_CACHE).then((cache) => cache.addAll(SHELL_FILES)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys
                .filter((key) => key.startsWith('gto-') && ![SHELL_CACHE, STATIC_CACHE, PRIVATE_CACHE].includes(key))
                .map((key) => caches.delete(key))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('message', (event) => {
    if (event.data === 'clear-private') {
        event.waitUntil ? event.waitUntil(caches.delete(PRIVATE_CACHE)) : caches.delete(PRIVATE_CACHE);
    }
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    if (request.method !== 'GET') return;

    const url = new URL(request.url);

    // Page navigations: always try the network first
    if (request.mode === 'navigate') {
        event.respondWith(handleNavigation(request, url));
        return;
    }

    // Static files: serve from cache, refresh in the background
    const isStatic = (url.origin === self.location.origin && STATIC_PATH.test(url.pathname))
        || STATIC_HOSTS.includes(url.hostname);
    if (isStatic) {
        event.respondWith(staleWhileRevalidate(request));
    }
});

async function handleNavigation(request, url) {
    const isIdCard = url.origin === self.location.origin && url.pathname.replace(/\/+$/, '') === ID_CARD_PATH;
    try {
        const response = await fetch(request);
        // Keep the graduate's latest ID card for offline use (only a real, successful card page)
        if (isIdCard && response.ok && !response.redirected) {
            const cache = await caches.open(PRIVATE_CACHE);
            await cache.put(ID_CARD_PATH, response.clone());
        }
        return response;
    } catch (err) {
        if (isIdCard) {
            const card = await caches.match(ID_CARD_PATH, { cacheName: PRIVATE_CACHE });
            if (card) return card;
        }
        return (await caches.match(OFFLINE_URL)) || new Response('لا يوجد اتصال بالإنترنت', {
            status: 503,
            headers: { 'Content-Type': 'text/plain; charset=utf-8' },
        });
    }
}

async function staleWhileRevalidate(request) {
    const cache = await caches.open(STATIC_CACHE);
    const cached = await cache.match(request);
    const network = fetch(request)
        .then((response) => {
            if (response && (response.ok || response.type === 'opaque')) {
                cache.put(request, response.clone());
            }
            return response;
        })
        .catch(() => cached);
    return cached || network;
}
