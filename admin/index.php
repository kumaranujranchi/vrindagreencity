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
        <div class="dashboard-grid">
            <!-- Stats Section -->
            <div class="stats-section">
                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--accent-blue); color: var(--accent-blue-text);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                            </path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $stats['total_leads']; ?></div>
                        <div class="stat-label">Total Contact Leads</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--accent-yellow); color: var(--accent-yellow-text);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $stats['new_leads']; ?></div>
                        <div class="stat-label">New Leads</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: var(--accent-green); color: var(--accent-green-text);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $stats['total_inquiries']; ?></div>
                        <div class="stat-label">Property Inquiries</div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: #f3e8ff; color: #7e22ce;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                            </path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo $stats['subscribers']; ?></div>
                        <div class="stat-label">Subscribers</div>
                    </div>
                </div>
            </div>

            <!-- Main Content (Left Column) -->
            <div class="main-content">
                <!-- Recent Leads Table -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            Recent Contact Leads
                        </div>
                        <a href="contact-leads.php" class="btn btn-primary btn-sm">
                            View All Leads
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                    <div class="table-container">
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
                                        <td colspan="6"
                                            style="text-align: center; padding: 40px; color: var(--text-secondary);">
                                            No leads found yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recent_leads as $lead): ?>
                                        <tr>
                                            <td>#<?php echo $lead['id']; ?></td>
                                            <td>
                                                <div style="font-weight: 600;"><?php echo htmlspecialchars($lead['name']); ?>
                                                </div>
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

                <!-- Mini Chart Section (New) -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Leads Trend (Last 7 Days)</div>
                    </div>
                    <div class="card-body">
                        <div class="mini-chart">
                            <!-- Mock Data for Visual Representation -->
                            <div class="chart-bar" style="height: 40%;" data-label="Mon"></div>
                            <div class="chart-bar" style="height: 65%;" data-label="Tue"></div>
                            <div class="chart-bar" style="height: 30%;" data-label="Wed"></div>
                            <div class="chart-bar" style="height: 85%;" data-label="Thu"></div>
                            <div class="chart-bar" style="height: 50%;" data-label="Fri"></div>
                            <div class="chart-bar" style="height: 75%;" data-label="Sat"></div>
                            <div class="chart-bar" style="height: 60%;" data-label="Sun"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar (Right Column) -->
            <div class="sidebar-content">
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Quick Actions</div>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="property-inquiries.php" class="action-btn">
                                <div class="action-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                    </svg>
                                </div>
                                <span>Properties</span>
                            </a>
                            <a href="contact-leads.php" class="action-btn">
                                <div class="action-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                        </path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </div>
                                <span>Leads</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Timeline -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Recent Activity</div>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php if (!empty($recent_leads)): ?>
                                <?php $count = 0;
                                foreach ($recent_leads as $lead):
                                    if ($count >= 3)
                                        break; ?>
                                    <div class="timeline-item">
                                        <div class="timeline-dot"></div>
                                        <div class="timeline-content">
                                            <h4>New Lead: <?php echo htmlspecialchars($lead['name']); ?></h4>
                                            <p>Submitted a contact inquiry.</p>
                                            <div class="timeline-time">
                                                <?php echo date('M d, H:i', strtotime($lead['created_at'])); ?></div>
                                        </div>
                                    </div>
                                    <?php $count++; endforeach; ?>
                            <?php else: ?>
                                <p style="color: var(--text-secondary); font-size: 14px;">No recent activity.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>