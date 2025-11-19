<?php
// Simple test file to check if admin setup is working
echo "<h1>Admin Panel Test</h1>";
echo "<hr>";

// Test 1: PHP Version
echo "<h2>1. PHP Version Check</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo (version_compare(phpversion(), '7.0.0', '>=')) ? "✅ PHP version is compatible<br>" : "❌ PHP version too old, need 7.0+<br>";
echo "<hr>";

// Test 2: Check if files exist
echo "<h2>2. Admin Files Check</h2>";
$files = [
    'config.php',
    'login.php',
    'index.php',
    'styles.css'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file NOT found<br>";
    }
}
echo "<hr>";

// Test 3: Database Connection
echo "<h2>3. Database Connection Test</h2>";
$db_host = 'localhost';
$db_user = 'u743570205_vindagreen';
$db_pass = 'Anuj@2025@2026';
$db_name = 'u743570205_vrindagreen';

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        echo "❌ Database Connection Failed: " . $conn->connect_error . "<br>";
    } else {
        echo "✅ Database Connected Successfully!<br>";
        
        // Test 4: Check if tables exist
        echo "<h2>4. Database Tables Check</h2>";
        $tables = ['admin_users', 'contact_leads', 'property_inquiries', 'newsletter_subscribers'];
        
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "✅ Table '$table' exists<br>";
            } else {
                echo "❌ Table '$table' NOT found<br>";
            }
        }
        
        // Test 5: Check admin user
        echo "<h2>5. Admin User Check</h2>";
        $result = $conn->query("SELECT COUNT(*) as count FROM admin_users");
        if ($result) {
            $row = $result->fetch_assoc();
            if ($row['count'] > 0) {
                echo "✅ Admin users found: " . $row['count'] . "<br>";
            } else {
                echo "❌ No admin users found in database<br>";
            }
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>6. Access Admin Panel</h2>";
echo "<a href='login.php' style='display:inline-block; padding:10px 20px; background:#0D9B4D; color:white; text-decoration:none; border-radius:5px;'>Go to Login Page</a>";
?>
