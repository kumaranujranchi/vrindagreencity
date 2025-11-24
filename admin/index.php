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
    <link rel="shortcut icon" type="image/x-icon" href="https://imagizer.imageshack.com/img923/9404/A1ADwj.png">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="page-header">
            <h1>Dashboard</h1>
            <p class="subtitle">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>! Here's
                what's happening today.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: #e0f2f1; color: #009688;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['total_leads']; ?></h3>
                    <p>Total Contact Leads</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #fff3e0; color: #ff9800;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['new_leads']; ?></h3>
                    <p>New Leads</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #e3f2fd; color: #2196f3;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['total_inquiries']; ?></h3>
                    <p>Property Inquiries</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: #f3e5f5; color: #9c27b0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3><?php echo $stats['subscribers']; ?></h3>
                    <p>Newsletter Subscribers</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Recent Contact Leads</h2>
                <a href="contact-leads.php" class="btn btn-primary">
                    View All Leads
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
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
                                <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                                    No leads found yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_leads as $lead): ?>
                                <tr>
                                    <td>#<?php echo $lead['id']; ?></td>
                                    <td>
                                        <div style="font-weight: 500;"><?php echo htmlspecialchars($lead['name']); ?></div>
                                    </td>
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