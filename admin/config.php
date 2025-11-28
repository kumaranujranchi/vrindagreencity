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
        // First attempt mysqli if available
        if (function_exists('mysqli_connect')) {
            // Enable error reporting for debugging if available
            if (function_exists('mysqli_report')) {
                mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            }
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($conn->connect_error) {
                error_log("MySQL Connection Error (mysqli): " . $conn->connect_error);
                throw new Exception("Connection failed (mysqli): " . $conn->connect_error);
            }
            $conn->set_charset("utf8mb4");
            return $conn; // mysqli object
        }

        // If mysqli not available, try PDO (pdo_mysql)
        if (class_exists('PDO')) {
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            return $pdo; // PDO instance
        }

        // No supported MySQL extension found
        throw new Exception("No supported MySQL extension available. Enable mysqli or pdo_mysql.");
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
    if (!$conn) return;
    // mysqli instance
    if (is_object($conn) && (get_class($conn) === 'mysqli' || $conn instanceof mysqli)) {
        $conn->close();
    }
    // PDO instance
    if (is_object($conn) && ($conn instanceof PDO)) {
        // PDO does not have close(); unset the var to close
        $conn = null;
    }
}

/**
 * Helper: Execute prepared statement for both mysqli and PDO
 * Returns associative array with success, insert_id, rows, error
 */
function dbPrepareAndExecute($conn, $sql, $params = [], $types = '') {
    try {
        // mysqli
        if (is_object($conn) && ($conn instanceof mysqli)) {
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                return ['success' => false, 'error' => $conn->error];
            }
            if (!empty($params) && !empty($types)) {
                // bind_param requires variables by reference
                $bind_names[] = $types;
                for ($i = 0; $i < count($params); $i++) {
                    $bind_name = 'bind' . $i;
                    $$bind_name = $params[$i];
                    $bind_names[] = &$$bind_name;
                }
                call_user_func_array([$stmt, 'bind_param'], $bind_names);
            }
            $stmt->execute();
            $insert_id = $conn->insert_id;
            $stmt->close();
            return ['success' => true, 'insert_id' => $insert_id];
        }

        // PDO
        if (is_object($conn) && ($conn instanceof PDO)) {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $insert_id = $conn->lastInsertId();
            return ['success' => true, 'insert_id' => $insert_id];
        }
        return ['success' => false, 'error' => 'Unsupported DB connection type'];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Helper: Run a SELECT query and return rows
 */
function dbQuery($conn, $sql, $params = [], $types = '') {
    try {
        if (is_object($conn) && ($conn instanceof mysqli)) {
            $result = $conn->query($sql);
            $rows = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
            }
            return ['success' => true, 'rows' => $rows];
        }
        if (is_object($conn) && ($conn instanceof PDO)) {
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['success' => true, 'rows' => $rows];
        }
        return ['success' => false, 'error' => 'Unsupported DB connection type'];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
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
