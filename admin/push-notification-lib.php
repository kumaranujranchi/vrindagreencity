<?php
/**
 * Push Notification Library
 * Handles sending push notifications using the Web Push protocol
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/push-config.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushNotificationService
{
  private $conn;
  private $webPush;

  public function __construct()
  {
    $this->conn = getDBConnection();

    // Initialize WebPush with VAPID keys
    $auth = [
      'VAPID' => [
        'subject' => VAPID_SUBJECT,
        'publicKey' => VAPID_PUBLIC_KEY,
        'privateKey' => VAPID_PRIVATE_KEY,
      ]
    ];

    $this->webPush = new WebPush($auth);
  }

  /**
   * Save a new subscription to database
   */
  public function saveSubscription($endpoint, $p256dh, $auth, $userAgent = null, $ipAddress = null)
  {
    $stmt = $this->conn->prepare("INSERT INTO push_subscribers (endpoint, p256dh_key, auth_token, user_agent, ip_address) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE last_active = CURRENT_TIMESTAMP, user_agent = ?, ip_address = ?");
    $stmt->bind_param("sssssss", $endpoint, $p256dh, $auth, $userAgent, $ipAddress, $userAgent, $ipAddress);

    if ($stmt->execute()) {
      return [
        'success' => true,
        'message' => 'Subscription saved successfully',
        'subscriber_id' => $this->conn->insert_id ?: $this->getSubscriberIdByEndpoint($endpoint)
      ];
    } else {
      return [
        'success' => false,
        'message' => 'Failed to save subscription: ' . $stmt->error
      ];
    }
  }

  /**
   * Remove a subscription from database
   */
  public function removeSubscription($endpoint)
  {
    $stmt = $this->conn->prepare("DELETE FROM push_subscribers WHERE endpoint = ?");
    $stmt->bind_param("s", $endpoint);

    if ($stmt->execute()) {
      return [
        'success' => true,
        'message' => 'Subscription removed successfully'
      ];
    } else {
      return [
        'success' => false,
        'message' => 'Failed to remove subscription'
      ];
    }
  }

  /**
   * Get all active subscribers
   */
  public function getAllSubscribers()
  {
    $result = $this->conn->query("SELECT * FROM push_subscribers ORDER BY subscribed_at DESC");
    $subscribers = [];

    while ($row = $result->fetch_assoc()) {
      $subscribers[] = $row;
    }

    return $subscribers;
  }

  /**
   * Get subscriber ID by endpoint
   */
  private function getSubscriberIdByEndpoint($endpoint)
  {
    $stmt = $this->conn->prepare("SELECT id FROM push_subscribers WHERE endpoint = ?");
    $stmt->bind_param("s", $endpoint);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['id'] : null;
  }

  /**
   * Send push notification to all subscribers
   */
  public function sendToAll($title, $body, $icon = null, $url = null, $badge = null)
  {
    // Create notification record
    $notificationId = $this->createNotification($title, $body, $icon, $url, $badge);

    if (!$notificationId) {
      return [
        'success' => false,
        'message' => 'Failed to create notification record'
      ];
    }

    // Get all subscribers
    $subscribers = $this->getAllSubscribers();

    if (empty($subscribers)) {
      return [
        'success' => false,
        'message' => 'No subscribers found'
      ];
    }

    $payload = json_encode([
      'title' => $title,
      'body' => $body,
      'icon' => $icon ?: PUSH_ICON_DEFAULT,
      'badge' => $badge ?: PUSH_BADGE_DEFAULT,
      'url' => $url ?: '/',
      'timestamp' => time()
    ]);

    $successCount = 0;
    $failCount = 0;
    $expiredSubscriptions = [];

    foreach ($subscribers as $subscriber) {
      try {
        $subscription = Subscription::create([
          'endpoint' => $subscriber['endpoint'],
          'publicKey' => $subscriber['p256dh_key'],
          'authToken' => $subscriber['auth_token']
        ]);

        $report = $this->webPush->sendOneNotification($subscription, $payload);

        if ($report->isSuccess()) {
          $successCount++;
          $this->logNotification($notificationId, $subscriber['id'], 'success', 'Delivered successfully');
        } else {
          $failCount++;
          $errorMessage = $report->getReason();

          // Check if subscription has expired (410 Gone)
          if ($report->isSubscriptionExpired()) {
            $expiredSubscriptions[] = $subscriber['id'];
            $this->logNotification($notificationId, $subscriber['id'], 'expired', $errorMessage);
          } else {
            $this->logNotification($notificationId, $subscriber['id'], 'failed', $errorMessage);
          }
        }
      } catch (Exception $e) {
        $failCount++;
        $this->logNotification($notificationId, $subscriber['id'], 'failed', $e->getMessage());
      }
    }

    // Remove expired subscriptions
    foreach ($expiredSubscriptions as $subscriberId) {
      $this->removeSubscriberById($subscriberId);
    }

    // Update notification stats
    $this->updateNotificationStats($notificationId, $successCount, $failCount);

    return [
      'success' => true,
      'message' => "Notification sent to {$successCount} subscribers",
      'stats' => [
        'total' => count($subscribers),
        'success' => $successCount,
        'failed' => $failCount,
        'expired' => count($expiredSubscriptions)
      ]
    ];
  }

  /**
   * Create notification record in database
   */
  private function createNotification($title, $body, $icon, $url, $badge)
  {
    $stmt = $this->conn->prepare("INSERT INTO push_notifications (title, body, icon, url, badge, status) VALUES (?, ?, ?, ?, ?, 'sent')");
    $stmt->bind_param("sssss", $title, $body, $icon, $url, $badge);

    if ($stmt->execute()) {
      return $this->conn->insert_id;
    }
    return false;
  }

  /**
   * Log individual notification delivery
   */
  private function logNotification($notificationId, $subscriberId, $status, $response)
  {
    $stmt = $this->conn->prepare("INSERT INTO push_notification_logs (notification_id, subscriber_id, status, response) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $notificationId, $subscriberId, $status, $response);
    $stmt->execute();
  }

  /**
   * Update notification statistics
   */
  private function updateNotificationStats($notificationId, $successCount, $failCount)
  {
    $totalSent = $successCount + $failCount;
    $stmt = $this->conn->prepare("UPDATE push_notifications SET total_sent = ?, total_success = ?, total_failed = ?, sent_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("iiii", $totalSent, $successCount, $failCount, $notificationId);
    $stmt->execute();
  }

  /**
   * Remove subscriber by ID
   */
  private function removeSubscriberById($subscriberId)
  {
    $stmt = $this->conn->prepare("DELETE FROM push_subscribers WHERE id = ?");
    $stmt->bind_param("i", $subscriberId);
    $stmt->execute();
  }

  /**
   * Get notification history
   */
  public function getNotificationHistory($limit = 50)
  {
    $stmt = $this->conn->prepare("SELECT * FROM push_notifications ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $notifications = [];
    while ($row = $result->fetch_assoc()) {
      $notifications[] = $row;
    }

    return $notifications;
  }

  /**
   * Get subscriber count
   */
  public function getSubscriberCount()
  {
    $result = $this->conn->query("SELECT COUNT(*) as count FROM push_subscribers");
    $row = $result->fetch_assoc();
    return $row['count'];
  }

  /**
   * Get statistics
   */
  public function getStats()
  {
    $subscriberCount = $this->getSubscriberCount();

    $result = $this->conn->query("SELECT COUNT(*) as count FROM push_notifications WHERE status = 'sent'");
    $row = $result->fetch_assoc();
    $notificationsSent = $row['count'];

    $result = $this->conn->query("SELECT SUM(total_success) as total FROM push_notifications");
    $row = $result->fetch_assoc();
    $totalDelivered = $row['total'] ?: 0;

    return [
      'subscribers' => $subscriberCount,
      'notifications_sent' => $notificationsSent,
      'total_delivered' => $totalDelivered
    ];
  }

  public function __destruct()
  {
    closeDBConnection($this->conn);
  }
}
?>