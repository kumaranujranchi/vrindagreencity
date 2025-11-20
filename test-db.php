<?php
// Detailed database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'u743570205_vrindagreen';
$pass = 'Anuj@2025@2026';
$db = 'u743570205_vrindagreen';

echo "<!DOCTYPE html><html><head><title>Database Test</title>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} code{background:#f4f4f4;padding:2px 5px;}</style></head><body>";

echo "<h2>🔍 Database Connection Test</h2>";
echo "<p><strong>Host:</strong> <code>$host</code></p>";
echo "<p><strong>Database:</strong> <code>$db</code></p>";
echo "<p><strong>User:</strong> <code>$user</code></p>";
echo "<p><strong>Password:</strong> <code>" . str_repeat('*', strlen($pass)) . "</code> (length: " . strlen($pass) . ")</p>";
echo "<hr>";

// Test 1: Basic connection without database
echo "<h3>Test 1: Connect to MySQL Server (without database)</h3>";
$conn_test = @new mysqli($host, $user, $pass);
if ($conn_test->connect_error) {
    echo "<p class='error'>❌ Failed: " . $conn_test->connect_error . "</p>";
    echo "<p class='warning'>⚠️ This means either:</p>";
    echo "<ul>";
    echo "<li>Password is wrong</li>";
    echo "<li>User doesn't exist</li>";
    echo "<li>User doesn't have access from 'localhost'</li>";
    echo "</ul>";
    echo "<h3>🔧 Fix in Hostinger:</h3>";
    echo "<ol>";
    echo "<li>Go to <strong>Databases → MySQL Databases</strong></li>";
    echo "<li>Check if user <code>$user</code> exists in 'Current Users' section</li>";
    echo "<li>If not, create the user with password: <code>$pass</code></li>";
    echo "<li>Make sure user is allowed to connect from <strong>localhost</strong></li>";
    echo "</ol>";
} else {
    echo "<p class='success'>✅ Connected to MySQL server successfully!</p>";
    $conn_test->close();
    
    // Test 2: Connection with database
    echo "<h3>Test 2: Connect to Database</h3>";
    $conn = @new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        echo "<p class='error'>❌ Failed: " . $conn->connect_error . "</p>";
        echo "<p class='warning'>⚠️ This means:</p>";
        echo "<ul>";
        echo "<li>Database <code>$db</code> doesn't exist, OR</li>";
        echo "<li>User <code>$user</code> doesn't have access to database <code>$db</code></li>";
        echo "</ul>";
        echo "<h3>🔧 Fix in Hostinger:</h3>";
        echo "<ol>";
        echo "<li>Go to <strong>Databases → MySQL Databases</strong></li>";
        echo "<li>In 'Add User To Database' section:</li>";
        echo "<li>Select User: <code>$user</code></li>";
        echo "<li>Select Database: <code>$db</code></li>";
        echo "<li>Click 'Add' button</li>";
        echo "<li>Select <strong>ALL PRIVILEGES</strong></li>";
        echo "<li>Click 'Make Changes'</li>";
        echo "</ol>";
    } else {
        echo "<p class='success'>✅ Connected to database successfully!</p>";
        
        // Test 3: Check tables
        echo "<h3>Test 3: Check Tables</h3>";
        $tables = ['contact_leads', 'admin_users', 'newsletter_subscribers', 'property_inquiries'];
        echo "<ul>";
        foreach ($tables as $table) {
            $result = @$conn->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "<li class='success'>✅ Table <code>$table</code> exists</li>";
            } else {
                echo "<li class='error'>❌ Table <code>$table</code> NOT found</li>";
            }
        }
        echo "</ul>";
        
        // Test 4: Count records
        echo "<h3>Test 4: Record Counts</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            $result = @$conn->query("SELECT COUNT(*) as count FROM $table");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "<li><code>$table</code>: " . $row['count'] . " records</li>";
            } else {
                echo "<li class='error'><code>$table</code>: Error reading table</li>";
            }
        }
        echo "</ul>";
        
        // Test 5: Check admin user
        echo "<h3>Test 5: Check Admin User</h3>";
        $result = @$conn->query("SELECT username FROM admin_users WHERE username = 'admin'");
        if ($result && $result->num_rows > 0) {
            echo "<p class='success'>✅ Admin user exists</p>";
        } else {
            echo "<p class='error'>❌ Admin user NOT found</p>";
            echo "<p>Run database.sql to create admin user</p>";
        }
        
        $conn->close();
    }
}

echo "<hr>";
echo "<p><strong>⚠️ Security Note:</strong> Delete this file after testing!</p>";
echo "<p>File location: <code>/test-db.php</code></p>";
echo "</body></html>";
?>
