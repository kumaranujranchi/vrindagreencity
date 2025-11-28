<?php
// (debugging removed) production friendly - errors are logged and handled

require_once 'config.php';
require_once 'push-notification-lib.php';
requireLogin();

// Initialize push service safely
try {
  $pushService = new PushNotificationService();
} catch (Throwable $e) {
  @file_put_contents(__DIR__ . '/error_log_push_notifications.txt', '[' . date('Y-m-d H:i:s') . '] ' . $e->__toString() . PHP_EOL, FILE_APPEND);
  die('<div style="padding:20px; background:#f8d7da;color:#721c24;border-radius:6px; margin:20px;">Could not initialize Push Notification Service: ' . htmlspecialchars($e->getMessage()) . '</div>');
}

// Handle delete subscriber
if (isset($_GET['delete'])) {
  $subscriber_id = (int) $_GET['delete'];
  $conn = getDBConnection();
  $stmt = $conn->prepare("DELETE FROM push_subscribers WHERE id = ?");
  $stmt->bind_param("i", $subscriber_id);

  if ($stmt->execute()) {
    $success_message = "Subscriber deleted successfully!";
  } else {
    $error_message = "Failed to delete subscriber.";
  }
  $stmt->close();
  closeDBConnection($conn);
}

$subscribers = $pushService->getAllSubscribers();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Push Subscribers - Vrinda Green City Admin</title>
  <link rel="shortcut icon" type="image/x-icon" href="https://imagizer.imageshack.com/img923/9404/A1ADwj.png">
  <link rel="stylesheet" href="styles.css">
  <style>
    .subscriber-details {
      font-size: 12px;
      color: #666;
    }

    .endpoint-preview {
      max-width: 300px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
  </style>
</head>

<body>
  <?php include 'header.php'; ?>

  <div class="container">
    <h1>📱 Push Notification Subscribers</h1>

    <?php if (isset($success_message)): ?>
      <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
      <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="card">
      <div class="card-header">
        <h2>All Subscribers (<?php echo count($subscribers); ?>)</h2>
        <a href="push-notifications.php" class="btn btn-primary">← Back to Notifications</a>
      </div>
      <div class="table-responsive">
        <table id="subscribersTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Endpoint</th>
              <th>User Agent</th>
              <th>IP Address</th>
              <th>Subscribed</th>
              <th>Last Active</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($subscribers)): ?>
              <tr>
                <td colspan="7" class="text-center">No subscribers found</td>
              </tr>
            <?php else: ?>
              <?php foreach ($subscribers as $subscriber): ?>
                <tr>
                  <td><?php echo $subscriber['id']; ?></td>
                  <td>
                    <div class="endpoint-preview" title="<?php echo htmlspecialchars($subscriber['endpoint']); ?>">
                      <?php echo htmlspecialchars(substr($subscriber['endpoint'], 0, 50)) . '...'; ?>
                    </div>
                  </td>
                  <td>
                    <div class="subscriber-details">
                      <?php
                      $ua = $subscriber['user_agent'];
                      if (strpos($ua, 'Chrome') !== false)
                        echo '🌐 Chrome';
                      elseif (strpos($ua, 'Firefox') !== false)
                        echo '🦊 Firefox';
                      elseif (strpos($ua, 'Safari') !== false)
                        echo '🧭 Safari';
                      elseif (strpos($ua, 'Edge') !== false)
                        echo '🔷 Edge';
                      else
                        echo '🌐 Browser';
                      ?>
                    </div>
                  </td>
                  <td><?php echo htmlspecialchars($subscriber['ip_address']); ?></td>
                  <td><?php echo date('d M Y H:i', strtotime($subscriber['subscribed_at'])); ?></td>
                  <td><?php echo date('d M Y H:i', strtotime($subscriber['last_active'])); ?></td>
                  <td>
                    <a href="?delete=<?php echo $subscriber['id']; ?>" class="btn btn-sm btn-danger"
                      onclick="return confirm('Are you sure you want to delete this subscriber?')">Delete</a>
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
    // Sidebar toggle
    (function () {
      var sidebarToggle = document.getElementById('sidebarToggle');
      var sidebar = document.getElementById('sidebar');
      if (!sidebarToggle || !sidebar) return;

      sidebarToggle.addEventListener('click', function (e) {
        e.stopPropagation();
        sidebar.classList.toggle('collapsed');
      });

      if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
      }
    })();
  </script>
</body>

</html>