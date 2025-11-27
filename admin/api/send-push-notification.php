<?php
/**
 * API Endpoint: Send Push Notification
 * Admin endpoint to send notifications to all subscribers
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../push-notification-lib.php';

// Require admin authentication
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['title']) || !isset($data['body'])) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Title and body are required']);
  exit;
}

$title = sanitize($data['title']);
$body = sanitize($data['body']);
$icon = isset($data['icon']) ? sanitize($data['icon']) : null;
$url = isset($data['url']) ? sanitize($data['url']) : null;
$badge = isset($data['badge']) ? sanitize($data['badge']) : null;

try {
  $pushService = new PushNotificationService();
  $result = $pushService->sendToAll($title, $body, $icon, $url, $badge);

  echo json_encode($result);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'message' => 'Server error: ' . $e->getMessage()
  ]);
}
?>