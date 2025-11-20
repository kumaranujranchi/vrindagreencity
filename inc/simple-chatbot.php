<?php
// Direct chatbot lead handler - ultra simple, no dependencies
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Database credentials
$host = 'localhost';
$user = 'u743570205_vindagreen';
$pass = 'Anuj@2025@2026';
$db = 'u743570205_vrindagreen';

// Log file
$logfile = __DIR__ . '/../admin/direct_chatbot.log';

function logMsg($msg) {
    global $logfile;
    file_put_contents($logfile, date('Y-m-d H:i:s') . ' - ' . $msg . "\n", FILE_APPEND);
}

logMsg("=== NEW REQUEST ===");
logMsg("POST: " . json_encode($_POST));

header('Content-Type: application/json');

try {
    // Get POST data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    logMsg("Name: $name, Email: $email, Phone: $phone");
    
    if (empty($name) || empty($email) || empty($phone)) {
        throw new Exception('Required fields missing');
    }
    
    // Connect to database
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        logMsg("DB connection failed: " . $conn->connect_error);
        throw new Exception('Database connection failed');
    }
    
    logMsg("DB connected successfully");
    
    // Sanitize
    $name = $conn->real_escape_string($name);
    $email = $conn->real_escape_string($email);
    $phone = $conn->real_escape_string($phone);
    $subject = $conn->real_escape_string($subject);
    $message = $conn->real_escape_string($message);
    
    // Insert query
    $sql = "INSERT INTO contact_leads (name, email, phone, subject, message, status, created_at) 
            VALUES ('$name', '$email', '$phone', '$subject', '$message', 'new', NOW())";
    
    logMsg("SQL: $sql");
    
    if ($conn->query($sql) === TRUE) {
        $lead_id = $conn->insert_id;
        logMsg("SUCCESS! Lead ID: $lead_id");
        
        echo json_encode([
            'type' => 'success',
            'message' => 'Lead saved successfully',
            'lead_id' => $lead_id
        ]);
    } else {
        logMsg("SQL Error: " . $conn->error);
        throw new Exception('Failed to insert: ' . $conn->error);
    }
    
    $conn->close();
    
} catch (Exception $e) {
    logMsg("ERROR: " . $e->getMessage());
    echo json_encode([
        'type' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
