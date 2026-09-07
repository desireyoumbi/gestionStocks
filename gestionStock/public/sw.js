```js
const CACHE_NAME = 'estockly-v1';

const urlsToCache = [
    '/',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

// Installation : met en cache les fichiers essentiels
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(urlsToCache);
        })
    );
});

// Activation : prend immédiatement le contrôle des pages ouvertes
self.addEventListener('activate', (event) => {
    event.waitUntil(
        self.clients.claim()
    );
});

// Interception des requêtes : sert depuis le cache si possible,
// sinon récupère la ressource depuis le réseau
self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});
```
