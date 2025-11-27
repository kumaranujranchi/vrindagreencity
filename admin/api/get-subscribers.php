<?php
/**
 * API Endpoint: Get All Subscribers
 * Returns list of all push notification subscribers
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../push-notification-lib.php';

// Require admin authentication
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  exit;
}

try {
  $pushService = new PushNotificationService();
  $subscribers = $pushService->getAllSubscribers();

  echo json_encode([
    'success' => true,
    'subscribers' => $subscribers,
    'count' => count($subscribers)
  ]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'message' => 'Server error: ' . $e->getMessage()
  ]);
}
?>