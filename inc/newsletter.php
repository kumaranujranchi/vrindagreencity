<?php
// Newsletter subscription handler
header('Content-Type: application/json');

// Include database configuration
require_once '../admin/config.php';

try {
    // Check if email is provided
    if (!isset($_POST['email']) || empty($_POST['email'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter your email address'
        ]);
        exit;
    }
    
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter a valid email address'
        ]);
        exit;
    }
    
    // Connect to database
    $conn = getDBConnection();
    // Check if email already exists
    $check = dbQuery($conn, "SELECT id, status FROM newsletter_subscribers WHERE email = " . (is_object($conn) && ($conn instanceof mysqli) ? "'" . $conn->real_escape_string($email) . "'" : "?"), is_object($conn) && ($conn instanceof mysqli) ? [] : [$email]);
    if (!$check['success']) {
        throw new Exception('Database error');
    }
    $rows = $check['rows'];
    if (count($rows) > 0) {
        $subscriber = $rows[0];
        
        if ($subscriber['status'] === 'active') {
            echo json_encode([
                'success' => false,
                'message' => 'This email is already subscribed to our newsletter'
            ]);
        } else {
            // Reactivate subscription
            if (is_object($conn) && ($conn instanceof mysqli)) {
                $updateStmt = $conn->prepare("UPDATE newsletter_subscribers SET status = 'active' WHERE email = ?");
                $updateStmt->bind_param("s", $email);
                $success = $updateStmt->execute();
            } else {
                $update = dbPrepareAndExecute($conn, "UPDATE newsletter_subscribers SET status = 'active' WHERE email = ?", [$email], 's');
                $success = $update['success'];
            }
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Welcome back! You have been resubscribed successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to subscribe. Please try again'
                ]);
            }
            $updateStmt->close();
        }
    } else {
        // Insert new subscriber
        if (is_object($conn) && ($conn instanceof mysqli)) {
            $insertStmt = $conn->prepare("INSERT INTO newsletter_subscribers (email, status) VALUES (?, 'active')");
            $insertStmt->bind_param("s", $email);
            $success = $insertStmt->execute();
        } else {
            $insert = dbPrepareAndExecute($conn, "INSERT INTO newsletter_subscribers (email, status) VALUES (?, 'active')", [$email], 's');
            $success = $insert['success'];
        }
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Thank you for subscribing to our newsletter!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to subscribe. Please try again'
            ]);
        }
        $insertStmt->close();
    }
    
    if (is_object($conn) && ($conn instanceof mysqli) && isset($stmt) && $stmt) {
        $stmt->close();
    }
    closeDBConnection($conn);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again later'
    ]);
    error_log("Newsletter subscription error: " . $e->getMessage());
}
?>
