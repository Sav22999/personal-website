/*
 * Fischietto service worker.
 * Makes the portal installable as a web app and keeps the interface available
 * when the connection is poor.
 *
 * - API calls (api/) always go to the network and are never cached: they contain personal data.
 * - Pages (navigation): network first, cached copy only when offline.
 * - Static files (CSS, JS, icons, fonts): served from cache and refreshed in the background.
 * - PDFs are not cached here (they can be large): the browser handles them.
 */
'use strict';

const CACHE = 'fischietto-v3.2.0';
const SHELL = ['./', 'index.html', 'manifest.webmanifest', 'assets/icons/icon.svg', 'assets/icons/icon-192.png'];

self.addEventListener('install', event => {
  event.waitUntil(caches.open(CACHE).then(c => c.addAll(SHELL)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys()
      .then(keys => Promise.all(keys.filter(k => k.startsWith('fischietto-') && k !== CACHE).map(k => caches.delete(k))))
      .then(() => self.clients.claim())
  );
});

function isStatic(url) {
  return /\.(css|js|svg|png|ico|webmanifest|woff2?)$/.test(url.pathname) || url.hostname === 'fonts.googleapis.com' || url.hostname === 'fonts.gstatic.com';
}

self.addEventListener('fetch', event => {
  const req = event.request;
  if (req.method !== 'GET') return;
  const url = new URL(req.url);
  const sameOrigin = url.origin === self.location.origin;

  // Personal data, uploads and PDFs: always straight to the network
  if (sameOrigin && (url.pathname.includes('/api/') || url.pathname.endsWith('.pdf') || url.pathname.endsWith('.php'))) return;

  if (req.mode === 'navigate') {
    event.respondWith(
      fetch(req)
        .then(res => {
          if (res.ok) { const copy = res.clone(); caches.open(CACHE).then(c => c.put(req, copy)); }
          return res;
        })
        .catch(() => caches.match(req).then(r => r || caches.match('./')))
    );
    return;
  }

  if ((sameOrigin || url.hostname.startsWith('fonts.')) && isStatic(url)) {
    event.respondWith(
      caches.open(CACHE).then(cache => cache.match(req).then(cached => {
        const network = fetch(req).then(res => {
          if (res.ok || res.type === 'opaque') cache.put(req, res.clone());
          return res;
        }).catch(() => cached);
        return cached || network;
      }))
    );
  }
});
