<?php
/**
 * API Endpoint: Unsubscribe from Push Notifications
 * Removes subscription from database
 */

header('Content-Type: application/json');

// Allow CORS for local testing
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../push-notification-lib.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data || !isset($data['endpoint'])) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Invalid request data']);
  exit;
}

$endpoint = $data['endpoint'];

try {
  $pushService = new PushNotificationService();
  $result = $pushService->removeSubscription($endpoint);

  echo json_encode($result);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode([
    'success' => false,
    'message' => 'Server error: ' . $e->getMessage()
  ]);
}
?>