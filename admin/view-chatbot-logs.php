<?php
require_once 'config.php';
requireLogin();

$logs = [];
$files = ['direct_chatbot.log', 'chatbot_leads.log'];
foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    $logs[$file] = file_exists($path) ? file_get_contents($path) : "(no log file found)";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Logs - Admin</title>
    <link rel="stylesheet" href="styles.css">
    <style> pre { background: #f8fafb; padding: 12px; border-radius: 8px; box-shadow: var(--shadow-sm); } </style>
    </head>
<body>
<?php include 'header.php'; ?>
<div class="container">
    <h1>Chatbot Logs</h1>
    <?php foreach ($logs as $file => $content): ?>
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header"><h3><?php echo htmlspecialchars($file); ?></h3></div>
            <div class="card-body">
                <pre><?php echo htmlspecialchars($content); ?></pre>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
