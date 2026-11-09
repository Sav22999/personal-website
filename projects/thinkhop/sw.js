/*
 * ThinkHop service worker: lets the game open without a connection.
 * Network first, so nothing goes stale: the saved copy is used only when there is no connection.
 * The API and the admin panel are never saved.
 */
'use strict';

const CACHE = 'thinkhop-24';
const FILES = ['./', 'engine.js', 'puzzles.js', 'twemoji.js', 'avatars.js', 'riddles.js', 'riddles-more.js', 'game.js', 'ui.js', 'account.js', 'social.js', 'mascots.js', 'help.js', 'i18n.js', 'i18n-puzzles.js', 'releases.js', 'style.css', 'privacy.html', 'manifest.webmanifest', 'icons/favicon.svg', 'icons/icon-192.png', 'fonts/stack-sans-notch.ttf', 'fonts/inter-latin-wght-normal.woff2', 'fonts/inter-latin-ext-wght-normal.woff2'];

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

// A push carries no data: it only wakes this worker, which asks the API what there is to tell
async function onPush() {
    const windows = await self.clients.matchAll({type: 'window', includeUncontrolled: true});
    if (windows.some(c => c.visibilityState === 'visible')) return; // Whoever is playing doesn't need a notification
    const res = await fetch('api/index.php?r=notify.pending', {credentials: 'same-origin'});
    if (res.status === 401) {
        const sub = await self.registration.pushManager.getSubscription();
        if (sub) await sub.unsubscribe(); // Signed out: this device has nothing to receive
        return;
    }
    const d = await res.json();
    await Promise.all((d.items || []).map(n => self.registration.showNotification('ThinkHop', {
        body: n.text, icon: 'icons/icon-192.png', badge: 'icons/favicon-32.png', tag: 'thinkhop-' + n.type,
    })));
}

self.addEventListener('push', event => {
    event.waitUntil(onPush().catch(() => {
    }));
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(self.clients.matchAll({type: 'window', includeUncontrolled: true}).then(list => {
        const open = list.find(c => 'focus' in c);
        return open ? open.focus() : self.clients.openWindow('./');
    }));
});
