<?php
require_once 'config.php';
requireLogin();

$conn = getDBConnection();

// Get last 10 leads from contact_leads table
$query = "SELECT * FROM contact_leads ORDER BY created_at DESC LIMIT 10";
$result = $conn->query($query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Chatbot Leads - Vrinda Green City Admin</title>
    <link rel="shortcut icon" type="image/x-icon" href="https://imagizer.imageshack.com/img923/9404/A1ADwj.png">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Test Chatbot Leads (Last 10 Entries)</h1>
        
        <div class="card">
            <div class="card-header">
                <h2>Recent Leads from contact_leads Table</h2>
            </div>
            <div class="table-responsive">
                <table id="chatbotLeadsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($row['message']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td><?php echo $row['created_at']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No leads found in database</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card" style="margin-top: 20px;">
            <div class="card-header">
                <h2>Database Connection Test</h2>
            </div>
            <div style="padding: 20px;">
                <p><strong>Database Host:</strong> <?php echo DB_HOST; ?></p>
                <p><strong>Database Name:</strong> <?php echo DB_NAME; ?></p>
                <p><strong>Connection Status:</strong> <span style="color: green;">✓ Connected</span></p>
                <p><strong>Total Leads in contact_leads:</strong> 
                    <?php 
                    $count_result = $conn->query("SELECT COUNT(*) as total FROM contact_leads");
                    $count = $count_result->fetch_assoc()['total'];
                    echo $count;
                    ?>
                </p>
            </div>
        </div>
        
        <div class="card" style="margin-top: 20px;">
            <div class="card-header">
                <h2>Test Chatbot Submission</h2>
            </div>
            <div style="padding: 20px;">
                <p>To test if chatbot is working:</p>
                <ol>
                    <li>Go to your website homepage</li>
                    <li>Click on the chatbot icon (bottom right)</li>
                    <li>Complete the conversation flow</li>
                    <li>Refresh this page to see if the lead appears</li>
                </ol>
                <p><strong>Note:</strong> Make sure to open browser console (F12) and check for any JavaScript errors.</p>
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

    // Skeleton loading for chatbot leads table
    (function(){
        try{
            var table = document.getElementById('chatbotLeadsTable');
            if(!table) return;

            var tbody = table.querySelector('tbody');
            if(!tbody || tbody.querySelector('.text-center')) return;

            var realContent = tbody.innerHTML;
            var skeletonRows = '';
            for(var i=0;i<3;i++){
                skeletonRows += '<tr>' +
                    '<td><div class="skeleton" style="height:14px;width:30px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:100px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:140px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:90px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:80px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:150px;border-radius:6px;"></div></td>' +
                    '<td><div class="skeleton" style="height:14px;width:60px;border-radius:6px;"></div></td>' +
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

<?php
closeDBConnection($conn);
?>
