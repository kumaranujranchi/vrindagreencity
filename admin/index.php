<?php
require_once 'config.php';
requireLogin();

$conn = getDBConnection();

// Get statistics
$stats = [
    'total_leads' => 0,
    'new_leads' => 0,
    'total_inquiries' => 0,
    'subscribers' => 0
];

// Contact leads
$result = $conn->query("SELECT COUNT(*) as count FROM contact_leads");
if ($result) {
    $stats['total_leads'] = $result->fetch_assoc()['count'];
}

$result = $conn->query("SELECT COUNT(*) as count FROM contact_leads WHERE status = 'new'");
if ($result) {
    $stats['new_leads'] = $result->fetch_assoc()['count'];
}

// Property inquiries
$result = $conn->query("SELECT COUNT(*) as count FROM property_inquiries");
if ($result) {
    $stats['total_inquiries'] = $result->fetch_assoc()['count'];
}

// Newsletter subscribers
$result = $conn->query("SELECT COUNT(*) as count FROM newsletter_subscribers WHERE status = 'active'");
if ($result) {
    $stats['subscribers'] = $result->fetch_assoc()['count'];
}

// Recent leads
$recent_leads = [];
$result = $conn->query("SELECT * FROM contact_leads ORDER BY created_at DESC LIMIT 10");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_leads[] = $row;
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Vrinda Green City Admin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #4CAF50;">📧</div>
                <div class="stat-content">
                    <h3><?php echo $stats['total_leads']; ?></h3>
                    <p>Total Contact Leads</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #FF9800;">🆕</div>
                <div class="stat-content">
                    <h3><?php echo $stats['new_leads']; ?></h3>
                    <p>New Leads</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #2196F3;">🏠</div>
                <div class="stat-content">
                    <h3><?php echo $stats['total_inquiries']; ?></h3>
                    <p>Property Inquiries</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: #9C27B0;">📬</div>
                <div class="stat-content">
                    <h3><?php echo $stats['subscribers']; ?></h3>
                    <p>Newsletter Subscribers</p>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Recent Contact Leads</h2>
                <a href="contact-leads.php" class="btn btn-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_leads)): ?>
                            <tr>
                                <td colspan="6" class="text-center">No leads found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_leads as $lead): ?>
                                <tr>
                                    <td><?php echo $lead['id']; ?></td>
                                    <td><?php echo htmlspecialchars($lead['name']); ?></td>
                                    <td><?php echo htmlspecialchars($lead['email']); ?></td>
                                    <td><?php echo htmlspecialchars($lead['phone'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $lead['status']; ?>">
                                            <?php echo ucfirst($lead['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($lead['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
