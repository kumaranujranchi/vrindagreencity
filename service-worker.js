/**
 * Service Worker for Push Notifications
 * Listens for push events and displays notifications
 */

// Service Worker version - increment when updating
const SW_VERSION = '1.0.0';
const CACHE_NAME = `vrinda-push-${SW_VERSION}`;

// Install event
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Installing...', SW_VERSION);
    self.skipWaiting(); // Activate immediately
});

// Activate event
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activating...', SW_VERSION);
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        })
    );
    return self.clients.claim();
});

// Push event - receive and display notification
self.addEventListener('push', (event) => {
    console.log('[Service Worker] Push received');

    let notificationData = {
        title: 'Vrinda Green City',
        body: 'You have a new notification',
        icon: '/assets/img/logo.png',
        badge: '/assets/img/badge.png',
        url: '/'
    };

    if (event.data) {
        try {
            const data = event.data.json();
            notificationData = {
                title: data.title || notificationData.title,
                body: data.body || notificationData.body,
                icon: data.icon || notificationData.icon,
                badge: data.badge || notificationData.badge,
                url: data.url || notificationData.url,
                timestamp: data.timestamp || Date.now()
            };
        } catch (e) {
            // If JSON parsing fails, use text content
            notificationData.body = event.data.text();
        }
    }

    const promiseChain = self.registration.showNotification(
        notificationData.title,
        {
            body: notificationData.body,
            icon: notificationData.icon,
            badge: notificationData.badge,
            data: {
                url: notificationData.url,
                timestamp: notificationData.timestamp
            },
            vibrate: [200, 100, 200],
            tag: 'vrinda-notification',
            renotify: true,
            requireInteraction: false,
            actions: [
                {
                    action: 'open',
                    title: 'View',
                    icon: '/assets/img/checkmark.png'
                },
                {
                    action: 'close',
                    title: 'Close',
                    icon: '/assets/img/close.png'
                }
            ]
        }
    );

    event.waitUntil(promiseChain);
});

// Notification click event
self.addEventListener('notificationclick', (event) => {
    console.log('[Service Worker] Notification clicked');

    event.notification.close();

    if (event.action === 'close') {
        // Do nothing, just close
        return;
    }

    // Default action or 'open' action
    const urlToOpen = event.notification.data.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((windowClients) => {
                // Check if there's already a window open with this URL
                for (let i = 0; i < windowClients.length; i++) {
                    const client = windowClients[i];
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }

                // If no window is open, open a new one
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Notification close event
self.addEventListener('notificationclose', (event) => {
    console.log('[Service Worker] Notification closed');
});

// Sync event (for background sync if needed in future)
self.addEventListener('sync', (event) => {
    console.log('[Service Worker] Background sync:', event.tag);
});

// Message event - communication with main page
self.addEventListener('message', (event) => {
    console.log('[Service Worker] Message received:', event.data);

    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
