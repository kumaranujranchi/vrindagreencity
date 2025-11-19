<?php
// Create Admin User Script
// Run this file ONCE to create admin user, then delete it

$db_host = 'localhost';
$db_user = 'u743570205_vindagreen';
$db_pass = 'Anuj@2025@2026';
$db_name = 'u743570205_vrindagreen';

echo "<h1>Create Admin User</h1>";
echo "<hr>";

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        die("❌ Database Connection Failed: " . $conn->connect_error);
    }
    
    echo "✅ Database Connected!<br><br>";
    
    // Check if admin_users table exists
    $result = $conn->query("SHOW TABLES LIKE 'admin_users'");
    if ($result->num_rows == 0) {
        echo "❌ Table 'admin_users' does not exist. Please import database.sql first!<br>";
        exit;
    }
    
    echo "✅ Table 'admin_users' exists<br><br>";
    
    // Delete existing admin user (if any)
    $conn->query("DELETE FROM admin_users WHERE username = 'admin'");
    echo "🗑️ Cleared existing admin user<br><br>";
    
    // Create new admin user
    $username = 'admin';
    $email = 'admin@vrindagreencity.com';
    $password = 'Admin@123';
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO admin_users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password_hash);
    
    if ($stmt->execute()) {
        echo "<div style='background:#d4edda; padding:20px; border-radius:8px; border-left:4px solid #28a745;'>";
        echo "<h2 style='color:#155724; margin:0 0 15px 0;'>✅ Admin User Created Successfully!</h2>";
        echo "<p style='margin:0;'><strong>Username:</strong> admin</p>";
        echo "<p style='margin:5px 0;'><strong>Password:</strong> Admin@123</p>";
        echo "<p style='margin:5px 0;'><strong>Email:</strong> admin@vrindagreencity.com</p>";
        echo "</div>";
        echo "<br>";
        echo "<a href='login.php' style='display:inline-block; padding:15px 30px; background:#0D9B4D; color:white; text-decoration:none; border-radius:6px; font-weight:600;'>Go to Login Page →</a>";
        echo "<br><br>";
        echo "<div style='background:#fff3cd; padding:15px; border-radius:8px; border-left:4px solid #ffc107; margin-top:20px;'>";
        echo "<strong>⚠️ IMPORTANT:</strong> Delete this file (create-admin.php) after creating the admin user for security!";
        echo "</div>";
    } else {
        echo "❌ Error creating admin user: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
