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
    $stmt = $conn->prepare("SELECT id, status FROM newsletter_subscribers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $subscriber = $result->fetch_assoc();
        
        if ($subscriber['status'] === 'active') {
            echo json_encode([
                'success' => false,
                'message' => 'This email is already subscribed to our newsletter'
            ]);
        } else {
            // Reactivate subscription
            $updateStmt = $conn->prepare("UPDATE newsletter_subscribers SET status = 'active' WHERE email = ?");
            $updateStmt->bind_param("s", $email);
            
            if ($updateStmt->execute()) {
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
        $insertStmt = $conn->prepare("INSERT INTO newsletter_subscribers (email, status) VALUES (?, 'active')");
        $insertStmt->bind_param("s", $email);
        
        if ($insertStmt->execute()) {
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
    
    $stmt->close();
    closeDBConnection($conn);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again later'
    ]);
    error_log("Newsletter subscription error: " . $e->getMessage());
}
?>
