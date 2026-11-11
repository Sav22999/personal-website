/*
 * Forkville service worker: the app shell opens fast and survives a flaky connection.
 * Network first, so nothing goes stale: the saved copy is used only when there is no connection.
 * The API is never saved (the game needs the server for every purchase).
 */
'use strict';

const CACHE = 'forkville-4';
const FILES = ['./', 'assets/app.js', 'assets/scene.js', 'assets/models.js', 'assets/sim.js', 'assets/editor.js', 'assets/i18n.js', 'assets/icons.js', 'assets/thumbs.js', 'assets/forkville.css',
    'vendor/three.module.min.js', 'manifest.webmanifest', 'icons/favicon.svg', 'icons/icon-192.png', 'assets/fonts/stack-sans-notch.ttf', 'privacy.html'];

self.addEventListener('install', event => {
    event.waitUntil(caches.open(CACHE).then(cache => cache.addAll(FILES)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', event => {
    event.waitUntil(caches.keys()
        .then(keys => Promise.all(keys.filter(key => key.startsWith('forkville-') && key !== CACHE).map(key => caches.delete(key))))
        .then(() => self.clients.claim()));
});

// The query string only busts the browser cache (?v=): one saved copy per file
const cacheKey = (url) => url.origin + url.pathname;

self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);
    if (event.request.method !== 'GET' || url.origin !== self.location.origin) return;
    if (url.pathname.includes('/api/') || url.pathname.includes('install-db') || url.pathname.includes('admin')) return;
    event.respondWith(fetch(event.request).then(response => {
        if (response.ok) {
            const copy = response.clone();
            caches.open(CACHE).then(cache => cache.put(cacheKey(url), copy));
        }
        return response;
    }).catch(() => caches.match(cacheKey(url)).then(saved => saved || Response.error())));
});
