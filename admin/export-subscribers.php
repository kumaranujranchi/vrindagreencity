<?php
require_once 'config.php';
requireLogin();

$conn = getDBConnection();

$result = $conn->query("SELECT * FROM newsletter_subscribers WHERE status = 'active' ORDER BY created_at DESC");
$subscribers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $subscribers[] = $row;
    }
}

closeDBConnection($conn);

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=newsletter_subscribers_' . date('Y-m-d') . '.csv');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, ['ID', 'Email', 'Status', 'Subscribed Date']);

// Add data rows
foreach ($subscribers as $subscriber) {
    fputcsv($output, [
        $subscriber['id'],
        $subscriber['email'],
        $subscriber['status'],
        date('Y-m-d H:i:s', strtotime($subscriber['created_at']))
    ]);
}

fclose($output);
exit();
?>
