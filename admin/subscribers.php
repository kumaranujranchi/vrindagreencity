<?php
require_once 'config.php';
requireLogin();

$conn = getDBConnection();

// Handle delete
if (isset($_GET['delete'])) {
    $subscriber_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM newsletter_subscribers WHERE id = ?");
    $stmt->bind_param("i", $subscriber_id);
    
    if ($stmt->execute()) {
        $success_message = "Subscriber deleted successfully!";
    } else {
        $error_message = "Failed to delete subscriber.";
    }
    $stmt->close();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $subscriber_id = (int)$_POST['subscriber_id'];
    $status = sanitize($_POST['status']);
    
    $stmt = $conn->prepare("UPDATE newsletter_subscribers SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $subscriber_id);
    
    if ($stmt->execute()) {
        $success_message = "Status updated successfully!";
    } else {
        $error_message = "Failed to update status.";
    }
    $stmt->close();
}

// Get filter
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

// Build query
$query = "SELECT * FROM newsletter_subscribers";
if ($status_filter) {
    $query .= " WHERE status = '" . $conn->real_escape_string($status_filter) . "'";
}
$query .= " ORDER BY created_at DESC";

$result = $conn->query($query);
$subscribers = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $subscribers[] = $row;
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Subscribers - Vrinda Green City Admin</title>
    <link rel="shortcut icon" type="image/x-icon" href="https://imagizer.imageshack.com/img923/9404/A1ADwj.png">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Newsletter Subscribers</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="filters">
            <div class="filter-group">
                <label>Filter by Status</label>
                <select class="form-control" onchange="window.location.href='subscribers.php?status=' + this.value">
                    <option value="">All Statuses</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="unsubscribed" <?php echo $status_filter === 'unsubscribed' ? 'selected' : ''; ?>>Unsubscribed</option>
                </select>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>All Subscribers (<?php echo count($subscribers); ?>)</h2>
                <a href="export-subscribers.php" class="btn btn-primary">Export CSV</a>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Subscribed Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($subscribers)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No subscribers found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($subscribers as $subscriber): ?>
                                <tr>
                                    <td><?php echo $subscriber['id']; ?></td>
                                    <td><?php echo htmlspecialchars($subscriber['email']); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="subscriber_id" value="<?php echo $subscriber['id']; ?>">
                                            <select name="status" class="form-control" onchange="this.form.submit()" style="width: 130px; padding: 5px;">
                                                <option value="active" <?php echo $subscriber['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                <option value="unsubscribed" <?php echo $subscriber['status'] === 'unsubscribed' ? 'selected' : ''; ?>>Unsubscribed</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td><?php echo date('d M Y H:i', strtotime($subscriber['created_at'])); ?></td>
                                    <td>
                                        <a href="?delete=<?php echo $subscriber['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this subscriber?')">Delete</a>
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
