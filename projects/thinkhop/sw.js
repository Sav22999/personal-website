/*
 * ThinkHop service worker: lets the game open without a connection.
 * Network first, so nothing goes stale: the saved copy is used only when there is no connection.
 * The API and the admin panel are never saved.
 */
'use strict';

const CACHE = 'thinkhop-13';
const FILES = ['./', 'engine.js', 'puzzles.js', 'riddles.js', 'riddles-more.js', 'game.js', 'ui.js', 'account.js', 'social.js', 'mascots.js', 'i18n.js', 'i18n-puzzles.js', 'releases.js', 'style.css', 'privacy.html', 'manifest.webmanifest', 'icons/favicon.svg', 'icons/icon-192.png', 'fonts/stack-sans-notch.ttf', 'fonts/inter-latin-wght-normal.woff2', 'fonts/inter-latin-ext-wght-normal.woff2'];

self.addEventListener('install', event => {
    event.waitUntil(caches.open(CACHE).then(cache => cache.addAll(FILES)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', event => {
    event.waitUntil(caches.keys()
        .then(keys => Promise.all(keys.filter(key => key.startsWith('thinkhop-') && key !== CACHE).map(key => caches.delete(key))))
        .then(() => self.clients.claim()));
});

// The query string only busts the browser cache (?v=): one saved copy per file
const cacheKey = (url) => url.origin + url.pathname;

self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);
    if (event.request.method !== 'GET' || url.origin !== self.location.origin) return;
    if (url.pathname.includes('/api/') || url.pathname.includes('admin')) return;

    event.respondWith(fetch(event.request).then(response => {
        if (response.ok) {
            const copy = response.clone();
            caches.open(CACHE).then(cache => cache.put(cacheKey(url), copy));
        }
        return response;
    }).catch(() => caches.match(cacheKey(url)).then(saved => saved || Response.error())));
});
