const CACHE_NAME = 'mwork-attendance-v6';
const APP_ASSETS = [
    '/manifest.webmanifest',
    '/mworkputih.png',
    '/mwork.png',
    '/mwork-icon-192-v6.png',
    '/mwork-icon-512-v6.png'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => Promise.allSettled(APP_ASSETS.map((asset) => cache.add(asset))))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);
    if (url.origin !== self.location.origin || !APP_ASSETS.includes(url.pathname)) return;

    event.respondWith(
        caches.match(event.request).then((cached) => cached || fetch(event.request))
    );
});
