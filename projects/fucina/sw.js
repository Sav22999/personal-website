/* Fucina — service worker: the app shell works offline, data always comes from the network. */
const CACHE = 'fucina-1.10.0';
const SHELL = ['./', 'index.html', 'manifest.webmanifest?v=2', 'assets/app.css?v=1.10.0', 'assets/fucina.css?v=1.10.0', 'assets/i18n.js?v=1.10.0', 'assets/api.js?v=1.10.0', 'assets/ui.js?v=1.10.0', 'assets/games.js?v=1.10.0', 'assets/drills.js?v=1.10.0', 'assets/app.js?v=1.10.0', 'assets/icons/icon-192.png?v=2', 'assets/fonts/nunito-latin.woff2', 'assets/fonts/fredoka-latin.woff2'];

self.addEventListener('install', e => {
    e.waitUntil(caches.open(CACHE).then(c => c.addAll(SHELL)).then(() => self.skipWaiting()));
});
self.addEventListener('activate', e => {
    e.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(k => k.startsWith('fucina-') && k !== CACHE).map(k => caches.delete(k)))).then(() => self.clients.claim()));
});
self.addEventListener('fetch', e => {
    const url = new URL(e.request.url);
    if (e.request.method !== 'GET' || url.origin !== location.origin) return;
    if (url.pathname.includes('/api/') || url.pathname.endsWith('.php')) return; // never cache the API or the certificates
    if (e.request.mode === 'navigate') {
        // Pages: network first, the cached shell when offline
        e.respondWith(fetch(e.request).catch(() => caches.match('index.html')));
        return;
    }
    e.respondWith(caches.match(e.request).then(hit => hit || fetch(e.request).then(res => {
        if (res.ok && url.pathname.includes('/assets/')) {
            const copy = res.clone();
            caches.open(CACHE).then(c => c.put(e.request, copy));
        }
        return res;
    })));
});
