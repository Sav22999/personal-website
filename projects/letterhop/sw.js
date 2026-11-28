/*
 * LetterHop service worker: lets the game open without a connection.
 * Network first, so nothing goes stale: the saved copy is used only when there is no connection.
 * All five dictionaries are saved, so the language can change offline too.
 */
'use strict';

const CACHE = 'letterhop-1';
const FILES = ['./', 'engine.js', 'game.js', 'i18n.js', 'releases.js', 'style.css', 'manifest.webmanifest', 'icons/favicon.svg', 'icons/icon-192.png', 'fonts/fredoka-latin.woff2', 'fonts/fredoka-latin-ext.woff2', 'fonts/nunito-latin.woff2', 'fonts/nunito-latin-ext.woff2', 'words/en.json', 'words/it.json', 'words/es.json', 'words/fr.json', 'words/de.json'];

self.addEventListener('install', event => {
    event.waitUntil(caches.open(CACHE).then(cache => cache.addAll(FILES)).then(() => self.skipWaiting()));
});

self.addEventListener('activate', event => {
    event.waitUntil(caches.keys()
        .then(keys => Promise.all(keys.filter(key => key.startsWith('letterhop-') && key !== CACHE).map(key => caches.delete(key))))
        .then(() => self.clients.claim()));
});

// The query string only busts the browser cache (?v=): one saved copy per file
const cacheKey = (url) => url.origin + url.pathname;

self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);
    if (event.request.method !== 'GET' || url.origin !== self.location.origin) return;

    event.respondWith(fetch(event.request).then(response => {
        if (response.ok) {
            const copy = response.clone();
            caches.open(CACHE).then(cache => cache.put(cacheKey(url), copy));
        }
        return response;
    }).catch(() => caches.match(cacheKey(url)).then(saved => saved || Response.error())));
});
