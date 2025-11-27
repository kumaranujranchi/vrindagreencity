<!-- Push Notification Subscription Widget -->
<div id="push-notification-widget" class="push-widget">
  <div class="push-widget-content">
    <div class="push-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
      </svg>
    </div>
    <div class="push-text">
      <h3>Stay Updated!</h3>
      <p id="push-status-text">🔕 Get notified about latest updates</p>
    </div>
    <div class="push-actions">
      <button id="push-subscribe-btn" class="push-btn push-btn-primary">
        Subscribe
      </button>
      <button id="push-unsubscribe-btn" class="push-btn push-btn-secondary" style="display: none;">
        Unsubscribe
      </button>
    </div>
  </div>
  <div id="push-message" class="push-message" style="display: none;"></div>
</div>

<style>
  .push-widget {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 25px;
    color: white;
    margin: 20px 0;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
  }

  .push-widget-content {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
  }

  .push-icon {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    padding: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .push-icon svg {
    color: white;
  }

  .push-text {
    flex: 1;
    min-width: 200px;
  }

  .push-text h3 {
    margin: 0 0 8px 0;
    font-size: 20px;
    font-weight: 700;
  }

  .push-text p {
    margin: 0;
    font-size: 14px;
    opacity: 0.95;
  }

  .push-actions {
    display: flex;
    gap: 10px;
  }

  .push-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .push-btn-primary {
    background: white;
    color: #667eea;
  }

  .push-btn-primary:hover {
    background: #f0f0f0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  .push-btn-secondary {
    background: transparent;
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.5);
  }

  .push-btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: white;
  }

  .push-message {
    margin-top: 15px;
    padding: 12px;
    border-radius: 6px;
    font-size: 14px;
  }

  .push-message.success {
    background: rgba(76, 175, 80, 0.2);
    border: 1px solid rgba(76, 175, 80, 0.5);
  }

  .push-message.error {
    background: rgba(244, 67, 54, 0.2);
    border: 1px solid rgba(244, 67, 54, 0.5);
  }

  .push-widget.subscribed {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  }

  @media (max-width: 768px) {
    .push-widget-content {
      flex-direction: column;
      text-align: center;
    }

    .push-actions {
      width: 100%;
      justify-content: center;
    }

    .push-btn {
      flex: 1;
    }
  }
</style>

<!-- Include the push notification script -->
<script src="/assets/js/push-notifications.js"></script>