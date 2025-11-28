<?php
// Direct chatbot lead handler - ultra simple, no dependencies
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Log file
$logfile = __DIR__ . '/../admin/direct_chatbot.log';

function logMsg($msg) {
    global $logfile;
    file_put_contents($logfile, date('Y-m-d H:i:s') . ' - ' . $msg . "\n", FILE_APPEND);
}

logMsg("=== NEW REQUEST ===");
logMsg("POST: " . json_encode($_POST));
// Use central DB config
require_once __DIR__ . '/../admin/config.php';
try {
    $conn = getDBConnection();
} catch (Exception $e) {
    logMsg('DB connection error: ' . $e->getMessage());
    echo json_encode(['type' => 'error', 'message' => 'DB connection error']);
    exit;
}

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
    
    logMsg("DB connected successfully");
    // Insert using mysqli or PDO based on connection type
    if (is_object($conn) && ($conn instanceof mysqli)) {
        // Sanitize using mysqli
        $name = $conn->real_escape_string($name);
        $email = $conn->real_escape_string($email);
        $phone = $conn->real_escape_string($phone);
        $subject = $conn->real_escape_string($subject);
        $message = $conn->real_escape_string($message);

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
    } elseif (is_object($conn) && ($conn instanceof PDO)) {
        // Use PDO prepared statements
        $sql = "INSERT INTO contact_leads (name, email, phone, subject, message, status, created_at) 
            VALUES (:name, :email, :phone, :subject, :message, 'new', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':subject' => $subject,
            ':message' => $message
        ]);
        $lead_id = $conn->lastInsertId();
        logMsg("SUCCESS! Lead ID: $lead_id");
        echo json_encode([
            'type' => 'success',
            'message' => 'Lead saved successfully',
            'lead_id' => $lead_id
        ]);
    } else {
        throw new Exception('Unsupported DB connection type');
    }
    
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
