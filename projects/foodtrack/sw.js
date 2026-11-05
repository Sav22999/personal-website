/*
 * FoodTrack service worker: makes the app installable and keeps the interface available with a poor connection.
 * - API calls and PHP pages always go to the network (personal data is never cached).
 * - Pages: network first, cached copy only when offline.
 * - Static files (CSS, JS, images): cache first, refreshed in the background.
 */
'use strict';

const CACHE = 'foodtrack-1.6.5';
const SHELL = ['./', 'index.html', 'manifest.webmanifest', 'assets/app.css?v=1.6.5', 'assets/i18n.js?v=1.6.5', 'assets/img/icons.js?v=1.6.5', 'assets/img/icons.svg?v=3', 'assets/api.js?v=1.6.5', 'assets/ui.js?v=1.6.5', 'assets/app.js?v=1.6.5', 'assets/vendor/zxing-0.21.3.min.js', 'assets/icons/icon-192.png', 'assets/icons/icon-color.png', 'assets/fonts/inter-latin-wght-normal.woff2'];

self.addEventListener('install', event => {
  event.waitUntil(caches.open(CACHE).then(c => c.addAll(SHELL)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', event => {
  event.waitUntil(caches.keys()
    .then(keys => Promise.all(keys.filter(k => k.startsWith('foodtrack-') && k !== CACHE).map(k => caches.delete(k))))
    .then(() => self.clients.claim()));
});

self.addEventListener('fetch', event => {
  const req = event.request;
  if (req.method !== 'GET') return;
  const url = new URL(req.url);
  if (url.origin !== self.location.origin) return;
  if (url.pathname.includes('/api/') || url.pathname.endsWith('.php') || url.pathname.endsWith('admin.html')) return;

  if (req.mode === 'navigate') {
    event.respondWith(fetch(req)
      .then(res => { if (res.ok) { const copy = res.clone(); caches.open(CACHE).then(c => c.put(req, copy)); } return res; })
      .catch(() => caches.match(req).then(r => r || caches.match('./'))));
    return;
  }

  if (/\.(css|js|png|jpg|jpeg|webp|svg|webmanifest|woff2)$/.test(url.pathname)) {
    event.respondWith(caches.open(CACHE).then(cache => cache.match(req).then(cached => {
      const network = fetch(req).then(res => { if (res.ok) cache.put(req, res.clone()); return res; }).catch(() => cached);
      return cached || network;
    })));
  }
});

// Tapping the "products expiring" notification opens (or focuses) the app
self.addEventListener('notificationclick', event => {
  event.notification.close();
  event.waitUntil(self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
    for (const c of list) if ('focus' in c) { c.navigate && c.navigate('./#/spaces'); return c.focus(); }
    return self.clients.openWindow('./#/spaces');
  }));
});
