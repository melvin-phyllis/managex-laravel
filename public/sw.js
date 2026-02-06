const CACHE_NAME = 'managex-v2';

// Use the SW scope as base path (works with any subdirectory)
const BASE_PATH = self.registration ? self.registration.scope : self.location.href.replace(/sw\.js$/, '');

// Install event - cache only the offline page
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('ManageX: Caching offline page');
                return cache.add(new Request(BASE_PATH + 'offline.html'));
            })
            .catch((error) => {
                console.log('ManageX: Cache failed', error);
            })
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - network first, fallback to cache
self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;
    if (!event.request.url.startsWith(self.location.origin)) return;

    // Skip dynamic routes
    if (event.request.url.includes('/admin/') ||
        event.request.url.includes('/login') ||
        event.request.url.includes('/logout') ||
        event.request.url.includes('/api/') ||
        event.request.url.includes('/sanctum/')) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                if (response.status === 200) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                }
                return response;
            })
            .catch(() => {
                return caches.match(event.request).then((cached) => {
                    if (cached) return cached;
                    if (event.request.mode === 'navigate') {
                        return caches.match(BASE_PATH + 'offline.html');
                    }
                    return new Response('Offline', { status: 503 });
                });
            })
    );
});

// Push notifications
self.addEventListener('push', (event) => {
    if (event.data) {
        const data = event.data.json();
        event.waitUntil(
            self.registration.showNotification(data.title || 'ManageX', {
                body: data.body || 'Nouvelle notification',
                icon: BASE_PATH + 'icons/icon-192x192.png',
                badge: BASE_PATH + 'icons/icon-72x72.png',
                data: { url: data.url || BASE_PATH }
            })
        );
    }
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url || BASE_PATH));
});
