const CACHE_NAME = 'managex-v4';

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
        event.request.url.includes('/employee/') ||
        event.request.url.includes('/login') ||
        event.request.url.includes('/logout') ||
        event.request.url.includes('/api/') ||
        event.request.url.includes('/push/') ||
        event.request.url.includes('/messaging/') ||
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

// ============================================================
// PUSH NOTIFICATIONS - Desktop (Windows/Mac) + Mobile (Android)
// ============================================================
self.addEventListener('push', (event) => {
    if (!event.data) return;

    let data;
    try {
        data = event.data.json();
    } catch (e) {
        data = {
            title: 'ManageX',
            body: event.data.text() || 'Nouvelle notification',
        };
    }

    const title = data.title || 'ManageX';
    const url = (data.data && data.data.url) ? data.data.url : (data.url || BASE_PATH);
    const type = (data.data && data.data.type) ? data.data.type : 'default';
    const playSound = data.data?.play_sound || false;
    const actions = getActionsForType(type);

    const options = {
        body: data.body || 'Nouvelle notification',
        icon: data.icon || BASE_PATH + 'icons/icon-192x192.png',
        badge: BASE_PATH + 'icons/icon-72x72.png',
        image: data.image || null,
        tag: data.tag || type || 'managex-default',
        renotify: true,
        vibrate: getVibrationPattern(type),
        requireInteraction: isImportant(type),
        actions: actions,
        data: {
            url: url,
            type: type,
            playSound: playSound,
            notificationId: data.data?.notificationId || null,
            dateOfArrival: Date.now(),
        },
        // Ne pas mettre silent pour que le son systeme joue
        silent: false,
        timestamp: Date.now(),
    };

    if (!options.image) delete options.image;

    event.waitUntil(
        self.registration.showNotification(title, options).then(() => {
            // Si play_sound est activé, envoyer un message aux clients pour jouer le son d'alarme
            if (playSound) {
                return clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
                    windowClients.forEach((client) => {
                        client.postMessage({
                            type: 'PLAY_ALARM_SOUND',
                            soundType: data.data?.sound_type || 'notification',
                            notificationType: type
                        });
                    });
                });
            }
        })
    );
});

// ============================================================
// NOTIFICATION CLICK - Ouvrir l'URL ou focus sur l'onglet existant
// ============================================================
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const url = event.notification.data?.url || BASE_PATH;
    const action = event.action;

    // Gestion des actions (boutons dans la notification)
    let targetUrl = url;
    if (action === 'dismiss') {
        return;
    } else if (action === 'check_in') {
        // Rediriger vers la page de pointage
        targetUrl = url;
    } else if (action === 'mark_read') {
        const notifId = event.notification.data?.notificationId;
        if (notifId) {
            fetch(BASE_PATH + 'admin/notifications/' + notifId + '/read', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            }).catch(() => { });
        }
        return;
    }

    // Essayer de trouver un onglet existant ManageX et le focus
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((windowClients) => {
                // Chercher un onglet ManageX deja ouvert
                for (const client of windowClients) {
                    if (client.url.startsWith(self.location.origin) && 'focus' in client) {
                        client.focus();
                        client.navigate(targetUrl);
                        return;
                    }
                }
                // Sinon ouvrir un nouvel onglet
                return clients.openWindow(targetUrl);
            })
    );
});

// Fermer la notification quand on fait "swipe" (Android)
self.addEventListener('notificationclose', (event) => {
    // Analytics ou logging si besoin
    console.log('[ManageX SW] Notification fermee:', event.notification.tag);
});

// ============================================================
// HELPERS - Vibration, Actions, Importance
// ============================================================

function getVibrationPattern(type) {
    switch (type) {
        case 'task_assigned':
        case 'leave_request':
            return [200, 100, 200]; // Double vibration
        case 'check_in_reminder':
            return [500, 200, 500, 200, 500, 200, 500]; // Vibration longue repetee (alarme)
        case 'late_arrival':
        case 'missing_evaluation_alert':
            return [300, 100, 300, 100, 300]; // Triple vibration urgente
        case 'new_message':
            return [100, 50, 100]; // Vibration courte
        case 'payroll_added':
            return [200, 100, 200, 100, 200]; // Triple vibration
        default:
            return [200, 100, 200]; // Double vibration standard
    }
}

function getActionsForType(type) {
    switch (type) {
        case 'task_assigned':
            return [
                { action: 'open_url', title: 'Voir la tache', icon: BASE_PATH + 'icons/icon-72x72.png' },
                { action: 'dismiss', title: 'Ignorer' },
            ];
        case 'leave_request':
            return [
                { action: 'open_url', title: 'Voir la demande', icon: BASE_PATH + 'icons/icon-72x72.png' },
                { action: 'dismiss', title: 'Plus tard' },
            ];
        case 'new_message':
            return [
                { action: 'open_url', title: 'Lire', icon: BASE_PATH + 'icons/icon-72x72.png' },
                { action: 'mark_read', title: 'Marquer lu' },
            ];
        case 'late_arrival':
            return [
                { action: 'open_url', title: 'Voir details', icon: BASE_PATH + 'icons/icon-72x72.png' },
            ];
        default:
            return [
                { action: 'open_url', title: 'Ouvrir', icon: BASE_PATH + 'icons/icon-72x72.png' },
                { action: 'dismiss', title: 'Fermer' },
            ];
    }
}

function isImportant(type) {
    const importantTypes = [
        'task_assigned',
        'leave_request',
        'late_arrival',
        'missing_evaluation_alert',
        'payroll_added',
        'check_in_reminder',
    ];
    return importantTypes.includes(type);
}
