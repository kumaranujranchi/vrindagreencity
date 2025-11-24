<?php
require_once 'config.php';
requireLogin();

$conn = getDBConnection();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $lead_id = (int)$_POST['lead_id'];
    $status = sanitize($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE contact_leads SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $lead_id);
    
    if ($stmt->execute()) {
        $success_message = "Status updated successfully!";
    } else {
        $error_message = "Failed to update status.";
    }
    $stmt->close();
}

// Handle delete
if (isset($_GET['delete'])) {
    $lead_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM contact_leads WHERE id = ?");
    $stmt->bind_param("i", $lead_id);
    
    if ($stmt->execute()) {
        $success_message = "Lead deleted successfully!";
    } else {
        $error_message = "Failed to delete lead.";
    }
    $stmt->close();
}

// Get filter
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Build query
$query = "SELECT * FROM contact_leads";
if ($status_filter) {
    $query .= " WHERE status = '" . $conn->real_escape_string($status_filter) . "'";
}
$query .= " ORDER BY created_at DESC";

$result = $conn->query($query);
$leads = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $leads[] = $row;
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Leads - Vrinda Green City Admin</title>
    <link rel="shortcut icon" type="image/x-icon" href="https://imagizer.imageshack.com/img923/9404/A1ADwj.png">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Contact Leads</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="filters">
            <div class="filter-group">
                <label>Filter by Status</label>
                <select class="form-control" onchange="window.location.href='contact-leads.php?status=' + this.value">
                    <option value="">All Statuses</option>
                    <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                    <option value="contacted" <?php echo $status_filter === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                    <option value="closed" <?php echo $status_filter === 'closed' ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>All Contact Leads (<?php echo count($leads); ?>)</h2>
            </div>
            <div class="table-responsive">
                <table id="leadsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($leads)): ?>
                            <tr>
                                <td colspan="9" class="text-center">No leads found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($leads as $lead): ?>
                                <tr>
                                    <td><?php echo $lead['id']; ?></td>
                                    <td><?php echo htmlspecialchars($lead['name']); ?></td>
                                    <td><?php echo htmlspecialchars($lead['email']); ?></td>
                                    <td><?php echo htmlspecialchars($lead['phone'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($lead['subject'] ?? 'N/A'); ?></td>
                                    <td style="max-width: 250px;">
                                        <?php 
                                        $message = $lead['message'];
                                        if (strlen($message) > 50) {
                                            echo htmlspecialchars(substr($message, 0, 50)) . '... ';
                                            echo '<a href="view-lead.php?id=' . $lead['id'] . '" style="color:#0D9B4D; font-weight:600;">Read More</a>';
                                        } else {
                                            echo htmlspecialchars($message);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="lead_id" value="<?php echo $lead['id']; ?>">
                                            <select name="status" class="form-control" onchange="this.form.submit()" style="width: 120px; padding: 5px;">
                                                <option value="new" <?php echo $lead['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                                <option value="contacted" <?php echo $lead['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                                <option value="closed" <?php echo $lead['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td><?php echo date('d M Y H:i', strtotime($lead['created_at'])); ?></td>
                                    <td>
                                        <a href="view-lead.php?id=<?php echo $lead['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                        <a href="?delete=<?php echo $lead['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this lead?')">Delete</a>
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
    // Sidebar toggle functionality
    (function(){
        var sidebarToggle = document.getElementById('sidebarToggle');
        var sidebar = document.getElementById('sidebar');
        if(!sidebarToggle || !sidebar) return;

        sidebarToggle.addEventListener('click', function(){
            sidebar.classList.toggle('collapsed');
        });
    })();

    // Skeleton loading for leads table
    (function(){
        try{
            var table = document.getElementById('leadsTable');
            if(!table) return;

            var tbody = table.querySelector('tbody');
            if(!tbody || tbody.querySelector('.text-center')) return; // Skip if empty state

            var realContent = tbody.innerHTML;
            var skeletonRows = '';
            for(var i=0;i<5;i++){
                skeletonRows += '<tr>' +
                    '<td><div class="skeleton" style="height:14px;width:30px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:100px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:140px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:90px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:80px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:120px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:70px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:100px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:120px;border-radius:6px;"></div></td>' +
                '</tr>';
            }

            tbody.innerHTML = skeletonRows;

            setTimeout(function(){
                tbody.innerHTML = realContent;
            }, 400);
        }catch(e){ console && console.warn(e); }
    })();
</script>
