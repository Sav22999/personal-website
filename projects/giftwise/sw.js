/* Giftwise — service worker: the app shell works offline, data always comes from the network. */
const CACHE = 'giftwise-1.1.1';
const SHELL = ['./', 'index.html', 'manifest.webmanifest', 'assets/app.css?v=1.1.1', 'assets/giftwise.css?v=1.1.1', 'assets/i18n.js?v=1.1.1', 'assets/api.js?v=1.1.1', 'assets/ui.js?v=1.1.1', 'assets/app.js?v=1.1.1', 'assets/icons/icon-192.png', 'assets/fonts/inter-latin-wght-normal.woff2'];

self.addEventListener('install', e => {
  e.waitUntil(caches.open(CACHE).then(c => c.addAll(SHELL)).then(() => self.skipWaiting()));
});
self.addEventListener('activate', e => {
  e.waitUntil(caches.keys().then(keys => Promise.all(keys.filter(k => k.startsWith('giftwise-') && k !== CACHE).map(k => caches.delete(k)))).then(() => self.clients.claim()));
});
self.addEventListener('fetch', e => {
  const url = new URL(e.request.url);
  if (e.request.method !== 'GET' || url.origin !== location.origin) return;
  if (url.pathname.includes('/api/')) return; // never cache the API
  if (e.request.mode === 'navigate') {
    // Pages (also the share target "?share=1"): network first, the cached shell when offline
    e.respondWith(fetch(e.request).catch(() => caches.match('index.html')));
    return;
  }
  e.respondWith(caches.match(e.request).then(hit => hit || fetch(e.request).then(res => {
    if (res.ok && url.pathname.includes('/assets/')) { const copy = res.clone(); caches.open(CACHE).then(c => c.put(e.request, copy)); }
    return res;
  })));
});
