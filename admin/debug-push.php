<?php
// Diagnostic script for Push Notification Service
// Do NOT commit this to production. Remove after debugging.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';
// Skip requireLogin() for diagnostics
try {
    require_once __DIR__ . '/push-notification-lib.php';
} catch (Throwable $t) {
    echo "Include Error: " . $t->getMessage() . PHP_EOL;
    file_put_contents(__DIR__ . '/error_log_push_notifications.txt', $t->__toString());
    exit(1);
}

try {
    $ps = new PushNotificationService();
    echo "PushNotificationService initialized successfully" . PHP_EOL;

    $stats = $ps->getStats();
    echo "Stats: " . print_r($stats, true) . PHP_EOL;
} catch (Throwable $t) {
    echo "Runtime Error: " . $t->getMessage() . PHP_EOL;
    echo "See: admin/error_log_push_notifications.txt" . PHP_EOL;
    file_put_contents(__DIR__ . '/error_log_push_notifications.txt', $t->__toString());
    exit(1);
}

echo "Diagnostics complete." . PHP_EOL;
