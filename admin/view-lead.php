<?php
require_once 'config.php';
requireLogin();

if (!isset($_GET['id'])) {
    header('Location: contact-leads.php');
    exit();
}

$lead_id = (int)$_GET['id'];
$conn = getDBConnection();

$stmt = $conn->prepare("SELECT * FROM contact_leads WHERE id = ?");
$stmt->bind_param("i", $lead_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: contact-leads.php');
    exit();
}

$lead = $result->fetch_assoc();
$stmt->close();
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Lead - Vrinda Green City Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Lead Details</h1>
        
        <div class="card">
            <div class="card-header">
                <h2>Contact Lead #<?php echo $lead['id']; ?></h2>
                <a href="contact-leads.php" class="btn btn-secondary">← Back to List</a>
            </div>
            <div style="padding: 25px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
                    <div>
                        <strong>Name:</strong><br>
                        <p style="margin: 5px 0 0; font-size: 16px;"><?php echo htmlspecialchars($lead['name']); ?></p>
                    </div>
                    <div>
                        <strong>Email:</strong><br>
                        <p style="margin: 5px 0 0; font-size: 16px;">
                            <a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>"><?php echo htmlspecialchars($lead['email']); ?></a>
                        </p>
                    </div>
                    <div>
                        <strong>Phone:</strong><br>
                        <p style="margin: 5px 0 0; font-size: 16px;">
                            <?php if ($lead['phone']): ?>
                                <a href="tel:<?php echo htmlspecialchars($lead['phone']); ?>"><?php echo htmlspecialchars($lead['phone']); ?></a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <strong>Subject:</strong><br>
                        <p style="margin: 5px 0 0; font-size: 16px;"><?php echo htmlspecialchars($lead['subject'] ?? 'N/A'); ?></p>
                    </div>
                    <div>
                        <strong>Status:</strong><br>
                        <p style="margin: 5px 0 0;">
                            <span class="badge badge-<?php echo $lead['status']; ?>" style="font-size: 14px; padding: 8px 15px;">
                                <?php echo ucfirst($lead['status']); ?>
                            </span>
                        </p>
                    </div>
                    <div>
                        <strong>Received:</strong><br>
                        <p style="margin: 5px 0 0; font-size: 16px;"><?php echo date('d M Y, h:i A', strtotime($lead['created_at'])); ?></p>
                    </div>
                </div>
                
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                    <strong>Message:</strong><br>
                    <p style="margin: 10px 0 0; padding: 20px; background: #f9f9f9; border-radius: 8px; line-height: 1.8; white-space: pre-wrap;"><?php echo htmlspecialchars($lead['message']); ?></p>
                </div>
                
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                    <strong>Last Updated:</strong>
                    <p style="margin: 5px 0 0; color: #666;"><?php echo date('d M Y, h:i A', strtotime($lead['updated_at'])); ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
