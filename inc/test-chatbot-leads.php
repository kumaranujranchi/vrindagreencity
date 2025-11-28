<?php
// Chatbot Lead Test - Check if leads are being saved
require_once '../admin/config.php';

$conn = getDBConnection();

// Get last 20 leads from contact_leads
$query = "SELECT * FROM contact_leads ORDER BY created_at DESC LIMIT 20";
$res = dbQuery($conn, $query);
$result = null;
if ($res['success']) {
    $result = $res['rows'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Leads Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #0D9B4D; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #0D9B4D; color: white; }
        tr:hover { background: #f9f9f9; }
        .success { color: green; font-weight: bold; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .refresh { background: #0D9B4D; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .refresh:hover { background: #0a7d3d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 Chatbot Leads Test</h1>
        
        <div class="info">
            <p><strong>Instructions:</strong></p>
            <ol>
                <li>Open your website in another tab: <a href="https://vrindagreencity.com" target="_blank">vrindagreencity.com</a></li>
                <li>Click on the chatbot icon (bottom right corner)</li>
                <li>Complete the conversation (provide name, email, phone, message)</li>
                <li>Come back here and click "Refresh" button</li>
                <li>Your new lead should appear at the top</li>
            </ol>
            <button class="refresh" onclick="location.reload()">🔄 Refresh Page</button>
        </div>

        <h2>Recent Leads (Last 20)</h2>
        <p>Total leads in database: 
            <?php 
            $countRes = dbQuery($conn, "SELECT COUNT(*) as total FROM contact_leads");
            $count = 0;
            if ($countRes['success'] && !empty($countRes['rows'])) {
                $count = $countRes['rows'][0]['total'];
            }
            echo "<strong class='success'>$count</strong>";
            ?>
        </p>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                    <?php if ($result && count($result) > 0): ?>
                        <?php foreach ($result as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['subject'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(substr($row['message'], 0, 50)) . '...'; ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #999;">No leads found yet. Try submitting through chatbot!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="info" style="margin-top: 30px; background: #fff3cd;">
            <h3>🔍 Debugging Tips:</h3>
            <ol>
                <li>Open browser console (F12) when using chatbot</li>
                <li>Check for any JavaScript errors</li>
                <li>Look for "Lead submitted successfully" message in console</li>
                <li>Check if POST request to /inc/contact.php is successful (Network tab)</li>
                <li>If no lead appears after submission, check: <code>/admin/contact_form_errors.log</code></li>
            </ol>
        </div>
    </div>
</body>
</html>
<?php
closeDBConnection($conn);
?>
