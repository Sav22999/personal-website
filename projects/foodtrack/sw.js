/*
 * FoodTrack service worker: makes the app installable and keeps the interface available with a poor connection.
 * - API calls and PHP pages always go to the network (personal data is never cached).
 * - Pages: network first, cached copy only when offline.
 * - Static files (CSS, JS, images): cache first, refreshed in the background.
 */
'use strict';

const CACHE = 'foodtrack-1.6.21';
const SHELL = ['./', 'index.html', 'manifest.webmanifest', 'assets/app.css?v=1.6.21', 'assets/i18n.js?v=1.6.21', 'assets/img/icons.js?v=1.6.21', 'assets/img/icons.svg?v=4', 'assets/api.js?v=1.6.21', 'assets/ui.js?v=1.6.21', 'assets/app.js?v=1.6.21', 'assets/vendor/zxing-0.21.3.min.js', 'assets/icons/icon-192.png', 'assets/icons/icon-color.png', 'assets/fonts/inter-latin-wght-normal.woff2'];

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
            .then(res => {
                if (res.ok) {
                    const copy = res.clone();
                    caches.open(CACHE).then(c => c.put(req, copy));
                }
                return res;
            })
            .catch(() => caches.match(req).then(r => r || caches.match('./'))));
        return;
    }

    if (/\.(css|js|png|jpg|jpeg|webp|svg|webmanifest|woff2)$/.test(url.pathname)) {
        event.respondWith(caches.open(CACHE).then(cache => cache.match(req).then(cached => {
            const network = fetch(req).then(res => {
                if (res.ok) cache.put(req, res.clone());
                return res;
            }).catch(() => cached);
            return cached || network;
        })));
    }
});

// A push carries no data: it only says that something changed, so the worker asks the API what is new.
// The position in the history is shared with the page through this cache, so nothing is told twice.
const STATE_CACHE = 'ft-state';

async function readState(cache) {
    const res = await cache.match('activity');
    return res ? res.json() : {after: 0, lang: ''};
}

async function onPush() {
    const windows = await self.clients.matchAll({type: 'window', includeUncontrolled: true});
    if (windows.some(c => c.visibilityState === 'visible')) return; // The open app tells the user by itself
    await new Promise(r => setTimeout(r, 4000)); // Lets a burst of changes end up in one notification
    const cache = await caches.open(STATE_CACHE);
    const state = await readState(cache);
    const res = await fetch('api/index.php?r=activity.pending&after=' + state.after, {
        credentials: 'same-origin',
        headers: state.lang ? {'X-Lang': state.lang} : {}
    });
    if (res.status === 401) {
        const sub = await self.registration.pushManager.getSubscription();
        if (sub) await sub.unsubscribe(); // Signed out: this device has nothing to receive
        return;
    }
    const d = await res.json();
    if (d.lastId) await cache.put('activity', new Response(JSON.stringify({after: d.lastId, lang: state.lang})));
    if (d.text) await self.registration.showNotification('FoodTrack', {
        body: d.text,
        icon: 'assets/icons/icon-192.png',
        badge: 'assets/icons/icon-mono.png',
        tag: 'ft-activity',
        data: {page: './#/history'}
    });
}

self.addEventListener('push', event => {
    event.waitUntil(onPush().catch(() => {
    }));
});

// Tapping a notification opens (or focuses) the app on the page it is about
self.addEventListener('notificationclick', event => {
    event.notification.close();
    const page = (event.notification.data && event.notification.data.page) || './#/spaces';
    event.waitUntil(self.clients.matchAll({type: 'window', includeUncontrolled: true}).then(list => {
        for (const c of list) if ('focus' in c) {
            c.navigate && c.navigate(page);
            return c.focus();
        }
        return self.clients.openWindow(page);
    }));
});
