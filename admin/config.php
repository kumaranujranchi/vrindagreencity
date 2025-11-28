<?php
// Database configuration for Hostinger
// IMPORTANT: Check these credentials in your Hostinger hPanel → MySQL Databases
// If you get "Access denied" error, verify password in Hostinger

define('DB_HOST', 'localhost');
define('DB_USER', 'u743570205_vindagreen');  // Fixed: removed 'r' from vrindagreen
define('DB_PASS', 'Anuj@2025@2026');
define('DB_NAME', 'u743570205_vrindagreen');  // Database name remains same

// Create database connection
function getDBConnection() {
    try {
        // Ensure mysqli extension is available
        if (!function_exists('mysqli_connect')) {
            throw new Exception("PHP 'mysqli' extension is not installed or enabled. Please enable it in your PHP configuration.");
        }

        // Enable error reporting for debugging if available
        if (function_exists('mysqli_report')) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        }
        
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            error_log("MySQL Connection Error: " . $conn->connect_error);
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        // Log detailed error
        error_log("Database connection failed - Host: " . DB_HOST . ", User: " . DB_USER . ", DB: " . DB_NAME);
        error_log("Error: " . $e->getMessage());
        
        // Show user-friendly error and provide diagnostic details
        $message = "Database connection error: " . $e->getMessage() .
            "<br><br>Please check:<br>" .
            "1. Database user has access to database<br>" .
            "2. Password is correct<br>" .
            "3. Database exists<br>" .
            "4. User has ALL PRIVILEGES on database";
        
        // Attempt to log to a server-side file in the admin folder (if possible)
        @file_put_contents(__DIR__ . '/error_log_push_notifications.txt', '[' . date('Y-m-d H:i:s') . '] ' . $e->__toString() . PHP_EOL, FILE_APPEND);
        die($message);
    }
}

// Close database connection
function closeDBConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Start session if not already started
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Check if user is logged in
function isLoggedIn() {
    initSession();
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirect to login if not authenticated
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}
?>
