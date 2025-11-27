/**
 * Push Notification JavaScript
 * Handles subscription management for push notifications
 */

const VAPID_PUBLIC_KEY = 'BB9GI9J5-oRxxrvXlHsmLeJ53rPPBYkKUmX0XMzDT3xLnzv2MZglhMZlljMvF6pHEqws8OLjvM5XpWXGbHueUsI';
const API_BASE_URL = '/admin/api';

class PushNotificationManager {
    constructor() {
        this.swRegistration = null;
        this.isSubscribed = false;
        this.init();
    }

    async init() {
        // Check if service workers are supported
        if (!('serviceWorker' in navigator)) {
            console.log('Service workers are not supported');
            this.showUnsupportedMessage();
            return;
        }

        // Check if Push API is supported
        if (!('PushManager' in window)) {
            console.log('Push notifications are not supported');
            this.showUnsupportedMessage();
            return;
        }

        try {
            // Register service worker
            this.swRegistration = await navigator.serviceWorker.register('/service-worker.js');
            console.log('Service Worker registered:', this.swRegistration);

            // Check current subscription status
            await this.updateSubscriptionStatus();

            // Set up event listeners
            this.setupEventListeners();

            // Auto-request permission if not already decided
            // This shows the browser's native permission popup automatically
            if (Notification.permission === 'default') {
                console.log('Auto-requesting notification permission...');
                // Small delay to ensure page is fully loaded
                setTimeout(() => {
                    this.subscribe();
                }, 1000); // 1 second delay after page load
            }
        } catch (error) {
            console.error('Service Worker registration failed:', error);
        }
    }

    async updateSubscriptionStatus() {
        const subscription = await this.swRegistration.pushManager.getSubscription();
        this.isSubscribed = subscription !== null;
        
        console.log('Subscription status:', this.isSubscribed);
        this.updateUI();
    }

    setupEventListeners() {
        const subscribeBtn = document.getElementById('push-subscribe-btn');
        const unsubscribeBtn = document.getElementById('push-unsubscribe-btn');

        if (subscribeBtn) {
            subscribeBtn.addEventListener('click', () => this.subscribe());
        }

        if (unsubscribeBtn) {
            unsubscribeBtn.addEventListener('click', () => this.unsubscribe());
        }
    }

    async subscribe() {
        try {
            // Request notification permission
            const permission = await Notification.requestPermission();
            
            if (permission !== 'granted') {
                alert('Please allow notifications to subscribe');
                return;
            }

            // Convert VAPID key
            const applicationServerKey = this.urlBase64ToUint8Array(VAPID_PUBLIC_KEY);

            // Subscribe to push notifications
            const subscription = await this.swRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: applicationServerKey
            });

            console.log('Push subscription:', subscription);

            // Send subscription to server
            await this.sendSubscriptionToServer(subscription);

            this.isSubscribed = true;
            this.updateUI();
            this.showMessage('Successfully subscribed to notifications!', 'success');
        } catch (error) {
            console.error('Failed to subscribe:', error);
            this.showMessage('Failed to subscribe. Please try again.', 'error');
        }
    }

    async unsubscribe() {
        try {
            const subscription = await this.swRegistration.pushManager.getSubscription();
            
            if (!subscription) {
                this.isSubscribed = false;
                this.updateUI();
                return;
            }

            // Unsubscribe from push service
            await subscription.unsubscribe();

            // Remove subscription from server
            await this.removeSubscriptionFromServer(subscription);

            this.isSubscribed = false;
            this.updateUI();
            this.showMessage('Successfully unsubscribed from notifications', 'success');
        } catch (error) {
            console.error('Failed to unsubscribe:', error);
            this.showMessage('Failed to unsubscribe. Please try again.', 'error');
        }
    }

    async sendSubscriptionToServer(subscription) {
        const response = await fetch(`${API_BASE_URL}/push-subscribe.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(subscription)
        });

        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to save subscription');
        }

        return data;
    }

    async removeSubscriptionFromServer(subscription) {
        const response = await fetch(`${API_BASE_URL}/push-unsubscribe.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ endpoint: subscription.endpoint })
        });

        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to remove subscription');
        }

        return data;
    }

    updateUI() {
        const widget = document.getElementById('push-notification-widget');
        const subscribeBtn = document.getElementById('push-subscribe-btn');
        const unsubscribeBtn = document.getElementById('push-unsubscribe-btn');
        const statusText = document.getElementById('push-status-text');

        if (!widget) return;

        if (this.isSubscribed) {
            widget.classList.add('subscribed');
            if (subscribeBtn) subscribeBtn.style.display = 'none';
            if (unsubscribeBtn) unsubscribeBtn.style.display = 'inline-block';
            if (statusText) statusText.textContent = '🔔 You are subscribed to notifications';
        } else {
            widget.classList.remove('subscribed');
            if (subscribeBtn) subscribeBtn.style.display = 'inline-block';
            if (unsubscribeBtn) unsubscribeBtn.style.display = 'none';
            if (statusText) statusText.textContent = '🔕 Get notified about latest updates';
        }
    }

    showMessage(message, type = 'info') {
        const messageEl = document.getElementById('push-message');
        if (!messageEl) {
            alert(message);
            return;
        }

        messageEl.textContent = message;
        messageEl.className = `push-message ${type}`;
        messageEl.style.display = 'block';

        setTimeout(() => {
            messageEl.style.display = 'none';
        }, 5000);
    }

    showUnsupportedMessage() {
        const widget = document.getElementById('push-notification-widget');
        if (widget) {
            widget.innerHTML = '<p style="color: #999; font-size: 14px;">⚠️ Push notifications are not supported on your browser</p>';
        }
    }

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.pushManager = new PushNotificationManager();
    });
} else {
    window.pushManager = new PushNotificationManager();
}
