<?php
// Debug Login Issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Login Debug Tool</h1>";
echo "<hr>";

$db_host = 'localhost';
$db_user = 'u743570205_vindagreen';
$db_pass = 'Anuj@2025@2026';
$db_name = 'u743570205_vrindagreen';

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        die("❌ Database Connection Failed: " . $conn->connect_error);
    }
    
    echo "<h2>✅ Database Connected</h2>";
    
    // Check if admin_users table exists
    echo "<h2>📋 Checking admin_users table</h2>";
    $result = $conn->query("SELECT * FROM admin_users WHERE username = 'admin'");
    
    if ($result && $result->num_rows > 0) {
        echo "✅ Admin user found in database<br><br>";
        
        $user = $result->fetch_assoc();
        echo "<strong>Database Record:</strong><br>";
        echo "ID: " . $user['id'] . "<br>";
        echo "Username: " . $user['username'] . "<br>";
        echo "Email: " . $user['email'] . "<br>";
        echo "Password Hash: " . substr($user['password'], 0, 30) . "...<br>";
        echo "Created: " . $user['created_at'] . "<br><br>";
        
        // Test password verification
        echo "<h2>🔐 Password Verification Test</h2>";
        $test_password = 'Admin@123';
        $stored_hash = $user['password'];
        
        echo "Testing password: <strong>$test_password</strong><br>";
        echo "Stored hash: <code>" . htmlspecialchars($stored_hash) . "</code><br><br>";
        
        if (password_verify($test_password, $stored_hash)) {
            echo "<div style='background:#d4edda; padding:20px; border-radius:8px; border-left:4px solid #28a745;'>";
            echo "✅ <strong>Password verification SUCCESSFUL!</strong><br>";
            echo "The password 'Admin@123' matches the stored hash.<br>";
            echo "Login should work with these credentials.";
            echo "</div>";
        } else {
            echo "<div style='background:#f8d7da; padding:20px; border-radius:8px; border-left:4px solid #dc3545;'>";
            echo "❌ <strong>Password verification FAILED!</strong><br>";
            echo "The stored hash does not match 'Admin@123'.<br>";
            echo "Need to recreate admin user with correct password.";
            echo "</div>";
            
            // Recreate admin user
            echo "<br><h3>🔧 Fixing Admin User...</h3>";
            
            // Delete old user
            $conn->query("DELETE FROM admin_users WHERE username = 'admin'");
            
            // Create new user with correct hash
            $new_hash = password_hash($test_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admin_users (username, email, password) VALUES (?, ?, ?)");
            $username = 'admin';
            $email = 'admin@vrindagreencity.com';
            $stmt->bind_param("sss", $username, $email, $new_hash);
            
            if ($stmt->execute()) {
                echo "<div style='background:#d4edda; padding:20px; border-radius:8px; border-left:4px solid #28a745; margin-top:15px;'>";
                echo "✅ Admin user recreated successfully!<br>";
                echo "Username: <strong>admin</strong><br>";
                echo "Password: <strong>Admin@123</strong><br>";
                echo "New hash: <code>" . htmlspecialchars($new_hash) . "</code>";
                echo "</div>";
                
                // Verify the new password
                if (password_verify($test_password, $new_hash)) {
                    echo "<br><div style='background:#d1ecf1; padding:15px; border-radius:8px; border-left:4px solid #17a2b8;'>";
                    echo "✅ New password verified successfully!";
                    echo "</div>";
                }
            } else {
                echo "❌ Failed to create new user: " . $stmt->error;
            }
            $stmt->close();
        }
        
    } else {
        echo "<div style='background:#fff3cd; padding:20px; border-radius:8px; border-left:4px solid #ffc107;'>";
        echo "⚠️ No admin user found in database!<br>";
        echo "Creating admin user now...";
        echo "</div><br>";
        
        // Create admin user
        $password_hash = password_hash('Admin@123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admin_users (username, email, password) VALUES (?, ?, ?)");
        $username = 'admin';
        $email = 'admin@vrindagreencity.com';
        $stmt->bind_param("sss", $username, $email, $password_hash);
        
        if ($stmt->execute()) {
            echo "<div style='background:#d4edda; padding:20px; border-radius:8px; border-left:4px solid #28a745;'>";
            echo "✅ Admin user created!<br>";
            echo "Username: <strong>admin</strong><br>";
            echo "Password: <strong>Admin@123</strong>";
            echo "</div>";
        } else {
            echo "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    }
    
    $conn->close();
    
    echo "<br><hr>";
    echo "<h2>🚀 Try Login Now</h2>";
    echo "<a href='login.php' style='display:inline-block; padding:15px 30px; background:#0D9B4D; color:white; text-decoration:none; border-radius:6px; font-weight:600;'>Go to Login Page →</a>";
    
    echo "<br><br>";
    echo "<div style='background:#fff3cd; padding:15px; border-radius:8px; border-left:4px solid #ffc107;'>";
    echo "⚠️ <strong>Security Note:</strong> Delete this debug.php file after login is working!";
    echo "</div>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
    background: #f5f5f5;
}
h1, h2 { color: #333; }
code {
    background: #f4f4f4;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 13px;
}
</style>
