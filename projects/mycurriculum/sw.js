/*
 * MyCurriculum — service worker.
 * The app needs the network for the CV itself (the API is never cached), but its shell, fonts and icons are kept so
 * it opens fast and, without a connection, shows a friendly offline page instead of the browser error.
 *  - JS and CSS: network first (an update is seen at once), the cached copy when offline;
 *  - fonts, icons, flags and libraries: cache first (they do not change; a new VERSION clears them);
 *  - pages: network first, offline.html when there is no connection.
 */
const VERSION = 'mycurriculum-4';
const SHELL = ['offline.html', 'manifest.webmanifest', 'assets/app.css', 'assets/cv.css', 'assets/app.js', 'assets/cv.js', 'assets/i18n.js', 'assets/pdf.js',
    'assets/public.js', 'icons/favicon.svg', 'icons/icon-192.png', 'fonts/Roboto-Regular.ttf', 'fonts/Roboto-Medium.ttf'];

self.addEventListener('install', (e) => {
    e.waitUntil(caches.open(VERSION).then((c) => c.addAll(SHELL)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', (e) => {
    e.waitUntil(caches.keys().then((keys) => Promise.all(keys.filter((k) => k.startsWith('mycurriculum-') && k !== VERSION).map((k) => caches.delete(k))))
        .then(() => self.clients.claim()));
});

function put(req, res) {
    if (res.ok && res.type === 'basic') {
        const copy = res.clone();
        caches.open(VERSION).then((c) => c.put(req, copy));
    }
    return res;
}

self.addEventListener('fetch', (e) => {
    const req = e.request;
    const url = new URL(req.url);
    if (req.method !== 'GET' || url.origin !== location.origin) return;
    if (url.pathname.includes('/api/') || url.pathname.endsWith('install-db.php')) return; // data and the installer: always the network
    if (req.mode === 'navigate') {
        e.respondWith(fetch(req).catch(() => caches.match('offline.html')));
        return;
    }
    if (/\.(js|mjs|css|webmanifest)$/.test(url.pathname) && !url.pathname.includes('/vendor/')) {
        e.respondWith(fetch(req).then((res) => put(req, res)).catch(() => caches.match(req, {ignoreSearch: true})));
        return;
    }
    if (/\/(fonts|icons|vendor|assets\/flags)\//.test(url.pathname)) {
        e.respondWith(caches.match(req, {ignoreSearch: true}).then((hit) => hit || fetch(req).then((res) => put(req, res))));
    }
});
