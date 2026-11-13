/* Fucina — service worker: the app shell works offline, data always comes from the network. */
const CACHE = 'fucina-1.24.0';
const SHELL = ['./', 'index.html', 'manifest.webmanifest?v=2', 'assets/app.css?v=1.24.0', 'assets/fucina.css?v=1.24.0', 'assets/i18n.js?v=1.24.0', 'assets/api.js?v=1.24.0', 'assets/ui.js?v=1.24.0', 'assets/games.js?v=1.24.0', 'assets/drills.js?v=1.24.0', 'assets/tutorials.js?v=1.24.0', 'assets/avatars.js?v=1.24.0', 'assets/app.js?v=1.24.0', 'assets/icons/icon-192.png?v=2', 'assets/fonts/nunito-latin.woff2', 'assets/fonts/fredoka-latin.woff2'];

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

// A push carries no data: it only says that something happened, so the worker asks the API what is new.
async function onPush() {
    const res = await fetch('api/index.php?r=notify.pending', {credentials: 'same-origin'});
    if (res.status === 401) {
        const sub = await self.registration.pushManager.getSubscription();
        if (sub) await sub.unsubscribe(); // Signed out: this device has nothing to receive
        return;
    }
    const d = await res.json();
    const windows = await self.clients.matchAll({type: 'window', includeUncontrolled: true});
    if (windows.some(c => c.visibilityState === 'visible')) return; // The open app says it by itself
    for (const n of d.items || []) {
        await self.registration.showNotification('Fucina', {body: n.text, icon: 'assets/icons/icon-192.png?v=2', badge: 'assets/icons/favicon-32.png?v=2', tag: n.tag, data: {page: n.page}});
    }
}

self.addEventListener('push', event => {
    event.waitUntil(onPush().catch(() => null));
});

// Tapping a notification opens (or focuses) the app on the page it is about
self.addEventListener('notificationclick', event => {
    event.notification.close();
    const page = (event.notification.data && event.notification.data.page) || './';
    event.waitUntil(self.clients.matchAll({type: 'window', includeUncontrolled: true}).then(list => {
        for (const c of list) if ('focus' in c) {
            if (c.navigate) c.navigate(page);
            return c.focus();
        }
        return self.clients.openWindow(page);
    }));
});
