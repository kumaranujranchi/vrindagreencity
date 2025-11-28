<?php
/**
 * Chatbot Lead Handler
 * Dedicated endpoint for capturing chatbot leads
 */

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../admin/chatbot_leads.log');

// Set JSON response header
header('Content-Type: application/json');

// Allow CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Log incoming request
error_log("=== CHATBOT LEAD SUBMISSION RECEIVED ===");
error_log("Time: " . date('Y-m-d H:i:s'));
error_log("Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . json_encode($_POST));

try {
    // Check if POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. Only POST allowed.');
    }
    
    // Check if data exists
    if (empty($_POST)) {
        throw new Exception('No data received');
    }
    
    // Get and validate data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    error_log("Extracted data - Name: $name, Email: $email, Phone: $phone, Subject: $subject");
    
    // Validate required fields
    if (empty($name)) {
        throw new Exception('Name is required');
    }
    
    if (empty($email)) {
        throw new Exception('Email is required');
    }
    
    if (empty($phone)) {
        throw new Exception('Phone is required');
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    // Sanitize data
    $name = htmlspecialchars(strip_tags($name));
    $email = htmlspecialchars(strip_tags($email));
    $phone = htmlspecialchars(strip_tags($phone));
    $subject = htmlspecialchars(strip_tags($subject));
    $message = htmlspecialchars(strip_tags($message));
    
    error_log("Data sanitized successfully");
    
    // Database connection
    require_once __DIR__ . '/../admin/config.php';
    
    error_log("Config file loaded");
    
    $conn = getDBConnection();
    
    error_log("Database connection established");
    
    // Insert using appropriate DB driver (mysqli or PDO)
    if (is_object($conn) && ($conn instanceof mysqli)) {
        // mysqli
        $sql = "INSERT INTO contact_leads (name, email, phone, subject, message, status, created_at) VALUES (?, ?, ?, ?, ?, 'new', NOW())";
        error_log("Preparing SQL (mysqli): $sql");
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare statement (mysqli): ' . $conn->error);
        }
        $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
        error_log("Parameters bound successfully (mysqli)");
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert lead (mysqli): ' . $stmt->error);
        }
        $lead_id = $conn->insert_id;
        $stmt->close();
    } elseif (is_object($conn) && ($conn instanceof PDO)) {
        // PDO
        $sql = "INSERT INTO contact_leads (name, email, phone, subject, message, status, created_at) VALUES (:name, :email, :phone, :subject, :message, 'new', NOW())";
        error_log("Preparing SQL (PDO): $sql");
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $errorInfo = $conn->errorInfo();
            throw new Exception('Failed to prepare statement (PDO): ' . json_encode($errorInfo));
        }
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':subject' => $subject,
            ':message' => $message
        ]);
        $lead_id = $conn->lastInsertId();
    } else {
        throw new Exception('Unsupported DB connection type');
    }
    
    error_log("✅ SUCCESS! Lead saved with ID: $lead_id");
    
    $stmt->close();
    closeDBConnection($conn);
    
    // Success response
    $response = [
        'type' => 'success',
        'message' => 'Thank you! Your inquiry has been submitted successfully.',
        'lead_id' => $lead_id,
        'data' => [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject
        ]
    ];
    
    error_log("Sending success response: " . json_encode($response));
    echo json_encode($response);
    
} catch (Exception $e) {
    // Error logging
    error_log("❌ ERROR: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Error response
    $response = [
        'type' => 'error',
        'message' => 'Sorry, there was an error submitting your inquiry. Please try again.',
        'error' => $e->getMessage() // Remove this in production
    ];
    
    http_response_code(500);
    echo json_encode($response);
}
?>
