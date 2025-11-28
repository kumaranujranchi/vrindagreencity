<?php
// Temporary: enable verbose error reporting to diagnose HTTP 500
// NOTE: remove these lines after debugging to avoid exposing sensitive details.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';
requireLogin();

// Check if vendor directory exists
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    die('<div style="padding: 20px; background: #f8d7da; color: #721c24; border-radius: 5px;">Error: Composer dependencies not found. Please run \'composer install\' or upload the vendor/ directory to your server.</div>');
}

require_once 'push-notification-lib.php';

try {
    $pushService = new PushNotificationService();
    $stats = $pushService->getStats();
    $notificationHistory = $pushService->getNotificationHistory(25);
} catch (Exception $e) {
    die('<div style="padding: 20px; background: #f8d7da; color: #721c24; border-radius: 5px;"><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>');
}

$success_message = '';
$error_message = '';

// Handle sending notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notification'])) {
  $title = sanitize($_POST['title']);
  $body = sanitize($_POST['body']);
  $icon = !empty($_POST['icon']) ? sanitize($_POST['icon']) : null;
  $url = !empty($_POST['url']) ? sanitize($_POST['url']) : null;
  $badge = !empty($_POST['badge']) ? sanitize($_POST['badge']) : null;

  if (!empty($title) && !empty($body)) {
    $result = $pushService->sendToAll($title, $body, $icon, $url, $badge);

    if ($result['success']) {
      $success_message = $result['message'] . " (Success: {$result['stats']['success']}, Failed: {$result['stats']['failed']})";
      // Refresh history
      $notificationHistory = $pushService->getNotificationHistory(25);
      $stats = $pushService->getStats();
    } else {
      $error_message = $result['message'];
    }
  } else {
    $error_message = "Title and message body are required!";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Push Notifications - Vrinda Green City Admin</title>
  <link rel="shortcut icon" type="image/x-icon" href="https://imagizer.imageshack.com/img923/9404/A1ADwj.png">
  <link rel="stylesheet" href="styles.css">
  <style>
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .stat-card:nth-child(2) {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-card:nth-child(3) {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stat-card h3 {
      margin: 0 0 10px 0;
      font-size: 14px;
      opacity: 0.9;
    }

    .stat-card .number {
      font-size: 36px;
      font-weight: bold;
      margin: 0;
    }

    .notification-form {
      background: #f8f9fa;
      padding: 25px;
      border-radius: 10px;
      margin-bottom: 30px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }

    .form-control {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
    }

    textarea.form-control {
      min-height: 100px;
      resize: vertical;
    }

    .btn-send {
      background: #28a745;
      color: white;
      padding: 12px 30px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-send:hover {
      background: #218838;
    }

    .history-table {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .history-table table {
      width: 100%;
      border-collapse: collapse;
    }

    .history-table th {
      background: #f8f9fa;
      padding: 12px;
      text-align: left;
      font-weight: 600;
      border-bottom: 2px solid #dee2e6;
    }

    .history-table td {
      padding: 12px;
      border-bottom: 1px solid #dee2e6;
    }

    .status-badge {
      padding: 4px 12px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 600;
    }

    .status-sent {
      background: #d4edda;
      color: #155724;
    }

    .status-failed {
      background: #f8d7da;
      color: #721c24;
    }

    .status-draft {
      background: #fff3cd;
      color: #856404;
    }

    .helper-text {
      font-size: 12px;
      color: #666;
      margin-top: 5px;
    }
  </style>
</head>

<body>
  <?php include 'header.php'; ?>

  <div class="container">
    <h1>📱 Push Notifications</h1>

    <?php if ($success_message): ?>
      <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
      <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="stats-grid">
      <div class="stat-card">
        <h3>Total Subscribers</h3>
        <div class="number"><?php echo $stats['subscribers']; ?></div>
      </div>
      <div class="stat-card">
        <h3>Notifications Sent</h3>
        <div class="number"><?php echo $stats['notifications_sent']; ?></div>
      </div>
      <div class="stat-card">
        <h3>Total Delivered</h3>
        <div class="number"><?php echo $stats['total_delivered']; ?></div>
      </div>
    </div>

    <!-- Send New Notification -->
    <div class="card">
      <div class="card-header">
        <h2>Send New Notification</h2>
      </div>
      <div class="notification-form">
        <form method="POST" action="">
          <div class="form-group">
            <label for="title">Notification Title *</label>
            <input type="text" id="title" name="title" class="form-control" required maxlength="100"
              placeholder="e.g., New Property Launch!">
            <div class="helper-text">Keep it short and attention-grabbing (max 100 characters)</div>
          </div>

          <div class="form-group">
            <label for="body">Message Body *</label>
            <textarea id="body" name="body" class="form-control" required maxlength="500"
              placeholder="e.g., Check out our latest premium properties in Vrinda Green City. Limited units available!"></textarea>
            <div class="helper-text">Provide clear and concise information (max 500 characters)</div>
          </div>

          <div class="form-group">
            <label for="url">Click URL (Optional)</label>
            <input type="url" id="url" name="url" class="form-control"
              placeholder="https://vrindagreencity.com/properties">
            <div class="helper-text">Where should users go when they click the notification?</div>
          </div>

          <div class="form-group">
            <label for="icon">Icon URL (Optional)</label>
            <input type="url" id="icon" name="icon" class="form-control"
              placeholder="/assets/img/notification-icon.png">
            <div class="helper-text">Image to display in the notification (leave blank for default logo)</div>
          </div>

          <button type="submit" name="send_notification" class="btn-send">
            🚀 Send to All Subscribers
          </button>
        </form>
      </div>
    </div>

    <!-- Notification History -->
    <div class="card">
      <div class="card-header">
        <h2>Recent Notifications</h2>
      </div>
      <div class="history-table">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Message</th>
              <th>Status</th>
              <th>Sent</th>
              <th>Success</th>
              <th>Failed</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($notificationHistory)): ?>
              <tr>
                <td colspan="8" class="text-center">No notifications sent yet</td>
              </tr>
            <?php else: ?>
              <?php foreach ($notificationHistory as $notification): ?>
                <tr>
                  <td><?php echo $notification['id']; ?></td>
                  <td><strong><?php echo htmlspecialchars($notification['title']); ?></strong></td>
                  <td>
                    <?php echo htmlspecialchars(substr($notification['body'], 0, 50)) . (strlen($notification['body']) > 50 ? '...' : ''); ?>
                  </td>
                  <td>
                    <span class="status-badge status-<?php echo $notification['status']; ?>">
                      <?php echo ucfirst($notification['status']); ?>
                    </span>
                  </td>
                  <td><?php echo $notification['total_sent']; ?></td>
                  <td style="color: #28a745; font-weight: 600;"><?php echo $notification['total_success']; ?></td>
                  <td style="color: #dc3545; font-weight: 600;"><?php echo $notification['total_failed']; ?></td>
                  <td><?php echo $notification['sent_at'] ? date('d M Y H:i', strtotime($notification['sent_at'])) : '-'; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    // Sidebar toggle functionality
    (function () {
      var sidebarToggle = document.getElementById('sidebarToggle');
      var sidebar = document.getElementById('sidebar');
      if (!sidebarToggle || !sidebar) return;

      function toggleSidebar() {
        sidebar.classList.toggle('collapsed');
      }

      sidebarToggle.addEventListener('click', function (e) {
        e.stopPropagation();
        toggleSidebar();
      });

      if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
      }
    })();
  </script>
</body>

</html>