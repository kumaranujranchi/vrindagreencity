<?php
require_once 'config.php';
requireLogin();

$conn = getDBConnection();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $inquiry_id = (int)$_POST['inquiry_id'];
    $status = sanitize($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE property_inquiries SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $inquiry_id);
    
    if ($stmt->execute()) {
        $success_message = "Status updated successfully!";
    } else {
        $error_message = "Failed to update status.";
    }
    $stmt->close();
}

// Handle delete
if (isset($_GET['delete'])) {
    $inquiry_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM property_inquiries WHERE id = ?");
    $stmt->bind_param("i", $inquiry_id);
    
    if ($stmt->execute()) {
        $success_message = "Inquiry deleted successfully!";
    } else {
        $error_message = "Failed to delete inquiry.";
    }
    $stmt->close();
}

// Get filter
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Build query
$query = "SELECT * FROM property_inquiries";
if ($status_filter) {
    $query .= " WHERE status = '" . $conn->real_escape_string($status_filter) . "'";
}
$query .= " ORDER BY created_at DESC";

$result = $conn->query($query);
$inquiries = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $inquiries[] = $row;
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Inquiries - Vrinda Green City Admin</title>
    <link rel="shortcut icon" type="image/x-icon" href="https://imagizer.imageshack.com/img923/9404/A1ADwj.png">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Property Inquiries</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="filters">
            <div class="filter-group">
                <label>Filter by Status</label>
                <select class="form-control" onchange="window.location.href='property-inquiries.php?status=' + this.value">
                    <option value="">All Statuses</option>
                    <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                    <option value="contacted" <?php echo $status_filter === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                    <option value="interested" <?php echo $status_filter === 'interested' ? 'selected' : ''; ?>>Interested</option>
                    <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>All Property Inquiries (<?php echo count($inquiries); ?>)</h2>
            </div>
            <div class="table-responsive">
                <table id="inquiriesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Property Type</th>
                            <th>Budget</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inquiries)): ?>
                            <tr>
                                <td colspan="10" class="text-center">No inquiries found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inquiries as $inquiry): ?>
                                <tr>
                                    <td><?php echo $inquiry['id']; ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['property_type'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($inquiry['budget'] ?? 'N/A'); ?></td>
                                    <td style="max-width: 200px;"><?php echo htmlspecialchars(substr($inquiry['message'] ?? '', 0, 50)) . (strlen($inquiry['message'] ?? '') > 50 ? '...' : ''); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="inquiry_id" value="<?php echo $inquiry['id']; ?>">
                                            <select name="status" class="form-control" onchange="this.form.submit()" style="width: 120px; padding: 5px;">
                                                <option value="new" <?php echo $inquiry['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                                <option value="contacted" <?php echo $inquiry['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                                <option value="interested" <?php echo $inquiry['status'] === 'interested' ? 'selected' : ''; ?>>Interested</option>
                                                <option value="closed" <?php echo $inquiry['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td><?php echo date('d M Y H:i', strtotime($inquiry['created_at'])); ?></td>
                                    <td>
                                        <a href="?delete=<?php echo $inquiry['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this inquiry?')">Delete</a>
                                    </td>
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

<script>
    // Sidebar toggle functionality with mobile support
    (function(){
        var sidebarToggle = document.getElementById('sidebarToggle');
        var sidebar = document.getElementById('sidebar');
        if(!sidebarToggle || !sidebar) return;

        function toggleSidebar() {
            var isMobile = window.innerWidth <= 768;
            if (isMobile) {
                // On mobile, toggle between hidden and visible
                sidebar.classList.toggle('collapsed');
            } else {
                // On desktop, toggle collapsed state
                sidebar.classList.toggle('collapsed');
            }
        }

        function closeSidebarOnMobile() {
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
            }
        }

        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });

        // Close sidebar when clicking on overlay (mobile only)
        sidebar.addEventListener('click', function(e) {
            // Only close if clicking on the overlay area (not on nav content)
            if (window.innerWidth <= 768 && e.target === sidebar) {
                closeSidebarOnMobile();
            }
        });

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768 &&
                !sidebar.contains(e.target) &&
                !sidebarToggle.contains(e.target) &&
                !sidebar.classList.contains('collapsed')) {
                closeSidebarOnMobile();
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                // On desktop, ensure sidebar is not collapsed by default
                sidebar.classList.remove('collapsed');
            } else {
                // On mobile, ensure sidebar starts collapsed
                sidebar.classList.add('collapsed');
            }
        });

        // Initialize on load
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
        }
    })();

    // Skeleton loading for inquiries table
    (function(){
        try{
            var table = document.getElementById('inquiriesTable');
            if(!table) return;

            var tbody = table.querySelector('tbody');
            if(!tbody || tbody.querySelector('.text-center')) return;

            var realContent = tbody.innerHTML;
            var skeletonRows = '';
            for(var i=0;i<5;i++){
                skeletonRows += '<tr>' +
                    '<td><div class="skeleton" style="height:14px;width:30px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:100px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:140px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:90px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:100px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:80px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:120px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:70px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:100px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:60px;border-radius:6px;"></div></td>' +
                '</tr>';
            }

            tbody.innerHTML = skeletonRows;

            setTimeout(function(){
                tbody.innerHTML = realContent;
            }, 400);
        }catch(e){ console && console.warn(e); }
    })();
</script>
