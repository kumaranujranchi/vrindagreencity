<?php
// Check if user is already subscribed (via localStorage)
?>
<style>
  /* Floating Notification Bell Widget */
  .push-notification-bell {
    position: fixed;
    bottom: 30px;
    left: 30px;
    z-index: 9999;
  }

  .notification-bell-trigger {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #0D9B4D 0%, #0a7a3d 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(13, 155, 77, 0.4);
    transition: all 0.3s ease;
    border: 3px solid #fff;
    position: relative;
  }

  .notification-bell-trigger:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 30px rgba(13, 155, 77, 0.6);
  }

  .notification-bell-trigger.subscribed {
    background: linear-gradient(135deg, #FAA432 0%, #e89420 100%);
    box-shadow: 0 4px 20px rgba(250, 164, 50, 0.4);
  }

  .notification-bell-trigger.subscribed:hover {
    box-shadow: 0 6px 30px rgba(250, 164, 50, 0.6);
  }

  .bell-icon {
    width: 28px;
    height: 28px;
    fill: #fff;
  }

  .bell-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff4444;
    color: #fff;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    font-size: 12px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
  }

  .bell-check {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #fff;
    color: #0D9B4D;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    font-size: 14px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  @keyframes pulse {

    0%,
    100% {
      transform: scale(1);
    }

    50% {
      transform: scale(1.2);
    }
  }

  .notification-popup {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 340px;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px);
    transition: all 0.3s ease;
  }

  .notification-popup.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
  }

  .popup-header {
    background: linear-gradient(135deg, #0D9B4D 0%, #0a7a3d 100%);
    color: #fff;
    padding: 20px;
    border-radius: 15px 15px 0 0;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .popup-header svg {
    width: 24px;
    height: 24px;
    fill: #fff;
  }

  .popup-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
  }

  .popup-body {
    padding: 25px;
  }

  .popup-message {
    color: #555;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 20px;
  }

  .subscribe-button {
    width: 100%;
    background: linear-gradient(135deg, #0D9B4D 0%, #0a7a3d 100%);
    color: #fff;
    border: none;
    padding: 14px 24px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .subscribe-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(13, 155, 77, 0.3);
  }

  .subscribe-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .unsubscribe-button {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    margin-top: 10px;
  }

  .unsubscribe-button:hover {
    box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
  }

  .subscribed-message {
    background: #e8f5e9;
    border-left: 4px solid #0D9B4D;
    padding: 15px;
    border-radius: 8px;
    color: #2e7d32;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .popup-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: #fff;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
  }

  .popup-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
  }

  .loading-spinner {
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  /* Tooltip */
  .bell-tooltip {
    position: absolute;
    bottom: 100%;
    right: 0;
    background: #333;
    color: #fff;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 13px;
    white-space: nowrap;
    margin-bottom: 10px;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
  }

  .notification-bell-trigger:hover .bell-tooltip {
    opacity: 1;
    visibility: visible;
  }

  .bell-tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    right: 20px;
    border: 6px solid transparent;
    border-top-color: #333;
  }

  @media (max-width: 768px) {
    .notification-popup {
      width: 90vw;
      max-width: 340px;
      right: 50%;
      transform: translateX(50%) translateY(20px);
    }

    .notification-popup.active {
      transform: translateX(50%) translateY(0);
    }

    .push-notification-bell {
      bottom: 20px;
      left: 20px;
    }

    .notification-bell-trigger {
      width: 55px;
      height: 55px;
    }
  }
</style>

<div class="push-notification-bell">
  <div class="notification-bell-trigger" id="bellTrigger">
    <svg class="bell-icon" viewBox="0 0 24 24">
      <path
        d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
    </svg>
    <span class="bell-badge" id="bellBadge" style="display: none;">!</span>
    <span class="bell-check" id="bellCheck" style="display: none;">✓</span>
    <div class="bell-tooltip">Get Notifications</div>
  </div>

  <div class="notification-popup" id="notificationPopup">
    <div class="popup-header">
      <svg viewBox="0 0 24 24">
        <path
          d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
      </svg>
      <h3>Stay Updated!</h3>
      <button class="popup-close" id="popupClose">×</button>
    </div>
    <div class="popup-body">
      <div id="notificationContent">
        <p class="popup-message">
          📢 Get instant updates about new property listings, price changes, and exclusive offers at Vrinda Green City!
        </p>
        <button class="subscribe-button" id="subscribeBtn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" />
          </svg>
          <span>Enable Notifications</span>
        </button>
        <button class="subscribe-button unsubscribe-button" id="unsubscribeBtn" style="display: none;">
          Unsubscribe
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const bellTrigger = document.getElementById('bellTrigger');
    const notificationPopup = document.getElementById('notificationPopup');
    const popupClose = document.getElementById('popupClose');
    const subscribeBtn = document.getElementById('subscribeBtn');
    const unsubscribeBtn = document.getElementById('unsubscribeBtn');
    const bellBadge = document.getElementById('bellBadge');
    const bellCheck = document.getElementById('bellCheck');

    // Check subscription status
    function checkSubscriptionStatus() {
      if ('serviceWorker' in navigator && 'PushManager' in window) {
        navigator.serviceWorker.ready.then(function (registration) {
          registration.pushManager.getSubscription().then(function (subscription) {
            if (subscription) {
              updateUIForSubscribed();
            } else {
              updateUIForUnsubscribed();
            }
          });
        });
      }
    }

    function updateUIForSubscribed() {
      bellTrigger.classList.add('subscribed');
      bellBadge.style.display = 'none';
      bellCheck.style.display = 'flex';
      subscribeBtn.style.display = 'none';
      unsubscribeBtn.style.display = 'flex';
      document.getElementById('notificationContent').innerHTML = `
            <div class="subscribed-message">
                ✓ You're subscribed to notifications!
            </div>
        `;
    }

    function updateUIForUnsubscribed() {
      bellTrigger.classList.remove('subscribed');
      bellBadge.style.display = 'flex';
      bellCheck.style.display = 'none';
      subscribeBtn.style.display = 'flex';
      unsubscribeBtn.style.display = 'none';
    }

    // Toggle popup
    bellTrigger.addEventListener('click', function () {
      notificationPopup.classList.toggle('active');
    });

    popupClose.addEventListener('click', function (e) {
      e.stopPropagation();
      notificationPopup.classList.remove('active');
    });

    // Close popup when clicking outside
    document.addEventListener('click', function (e) {
      if (!bellTrigger.contains(e.target) && !notificationPopup.contains(e.target)) {
        notificationPopup.classList.remove('active');
      }
    });

    // Subscribe button click - will be handled by push-notifications.js
    subscribeBtn.addEventListener('click', function () {
      subscribeBtn.disabled = true;
      subscribeBtn.innerHTML = '<div class="loading-spinner"></div><span>Subscribing...</span>';

      // Trigger the global subscribe function from push-notifications.js
      if (window.subscribeToPushNotifications) {
        window.subscribeToPushNotifications();
      }
    });

    // Unsubscribe button click
    unsubscribeBtn.addEventListener('click', function () {
      if (window.unsubscribeFromPushNotifications) {
        window.unsubscribeFromPushNotifications();
      }
    });

    // Initial check
    checkSubscriptionStatus();

    // Listen for subscription changes from push-notifications.js
    window.addEventListener('pushSubscriptionChanged', function (e) {
      if (e.detail.subscribed) {
        updateUIForSubscribed();
      } else {
        updateUIForUnsubscribed();
      }
    });
  });
</script>