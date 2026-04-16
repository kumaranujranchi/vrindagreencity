<?php
// (debugging removed) -- production friendly - errors are logged and handled

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
                <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['subscribers']; ?></h3>
                    <p>Subscribers</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #ecfdf5; color: #059669;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 2L11 13"></path>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['notifications_sent']; ?></h3>
                    <p>Sent</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #fff7ed; color: #ea580c;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['total_delivered']; ?></h3>
                    <p>Delivered</p>
                </div>
            </div>
        </div>

        <!-- Send New Notification -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Send New Notification</div>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Notification Title *</label>
                        <input type="text" name="title" class="form-control" required maxlength="100"
                            placeholder="e.g., New Property Launch!" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Message Body *</label>
                        <textarea name="body" class="form-control" required maxlength="500"
                            placeholder="e.g., Check our latest premium properties!" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; min-height: 100px;"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Click URL (Optional)</label>
                        <input type="url" name="url" class="form-control" placeholder="https://vrindagreencity.com/properties" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px;">
                    </div>

                    <button type="submit" name="send_notification" class="btn btn-primary">
                        🚀 Send to All Subscribers
                    </button>
                </form>
            </div>
        </div>

        <!-- Notification History -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">Recent Notifications</div>
            </div>
            <div class="table-responsive">
                <table id="notificationHistoryTable">
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
                                    <td style="font-weight: 600;"><?php echo htmlspecialchars($notification['title']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars(substr($notification['body'], 0, 50)) . (strlen($notification['body']) > 50 ? '...' : ''); ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $notification['status']; ?>">
                                            <?php echo ucfirst($notification['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $notification['total_sent']; ?></td>
                                    <td style="color: var(--primary); font-weight: 600;"><?php echo $notification['total_success']; ?></td>
                                    <td style="color: #ef4444; font-weight: 600;"><?php echo $notification['total_failed']; ?></td>
                                    <td><?php echo $notification['sent_at'] ? date('d M Y H:i', strtotime($notification['sent_at'])) : '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.showSkeletons) {
                window.showSkeletons('notificationHistoryTable', 8);
            }
        });
    </script>
</body>
</html>