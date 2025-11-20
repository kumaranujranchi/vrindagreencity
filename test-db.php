<?php
// Simple database connection test
$host = 'localhost';
$user = 'u743570205_vrindagreen';
$pass = 'Anuj@2025@2026';
$db = 'u743570205_vrindagreen';

echo "<h2>Database Connection Test</h2>";
echo "<p><strong>Host:</strong> $host</p>";
echo "<p><strong>Database:</strong> $db</p>";
echo "<p><strong>User:</strong> $user</p>";
echo "<hr>";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo "<p style='color: red;'><strong>❌ Connection Failed:</strong> " . $conn->connect_error . "</p>";
    echo "<p>Possible issues:</p>";
    echo "<ul>";
    echo "<li>Database user doesn't have access to this database</li>";
    echo "<li>Password is incorrect</li>";
    echo "<li>Database doesn't exist</li>";
    echo "</ul>";
} else {
    echo "<p style='color: green;'><strong>✅ Connected Successfully!</strong></p>";
    
    // Test if tables exist
    $tables = ['contact_leads', 'admin_users', 'newsletter_subscribers', 'property_inquiries'];
    echo "<h3>Table Check:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<li style='color: green;'>✅ Table '$table' exists</li>";
        } else {
            echo "<li style='color: red;'>❌ Table '$table' NOT found</li>";
        }
    }
    echo "</ul>";
    
    // Count records
    echo "<h3>Record Counts:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        $result = $conn->query("SELECT COUNT(*) as count FROM $table");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<li>$table: " . $row['count'] . " records</li>";
        }
    }
    echo "</ul>";
}

if ($conn) {
    $conn->close();
}

echo "<hr>";
echo "<p><strong>Note:</strong> Delete this file after testing for security!</p>";
?>
