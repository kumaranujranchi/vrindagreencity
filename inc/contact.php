<?php
/*
 *  CONFIGURE EVERYTHING HERE
 */

// Include database configuration
require_once '../admin/config.php';

// an email address that will be in the From field of the email.
$from = 'Demo contact form';

// an email address that will receive the email with the output of the form
$sendTo = 'tanberr90@gmail.com'; // Add Your email here

// subject of the email
$subject = 'New message from contact form';

// form field names and their translations.
// array variable name => Text to appear in the email
$fields = array('name' => 'Name', 'email' => 'Email', 'phone' => 'Phone', 'subject' => 'Subject', 'message' => 'Message');

// message that will be displayed when everything is OK :)
$okMessage = 'Contact form successfully submitted. Thank you, I will get back to you soon!';

// If something goes wrong, we will display this message.
$errorMessage = 'There was an error while submitting the form. Please try again later';

/*
 *  LET'S DO THE SENDING
 */

// Enable error logging
error_reporting(E_ALL & ~E_NOTICE);
ini_set('log_errors', 1);
ini_set('error_log', '../admin/contact_form_errors.log');

try {

    if (count($_POST) == 0)
        throw new \Exception('Form is empty');

    // Get form data
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $form_subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        throw new \Exception('Please fill all required fields');
    }

    // Save to database
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO contact_leads (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $form_subject, $message);
        
        if (!$stmt->execute()) {
            error_log("SQL Error: " . $stmt->error);
            throw new Exception('Failed to save lead to database');
        }
        
        $lead_id = $conn->insert_id;
        error_log("Lead saved successfully with ID: " . $lead_id);
        
        $stmt->close();
        closeDBConnection($conn);
    } catch (Exception $db_error) {
        // Log error and throw exception to be caught by outer catch block
        error_log("Database error: " . $db_error->getMessage());
        throw new Exception('Database error occurred. Please try again later.');
    }

    $emailText = "You have a new message from your contact form\n=============================\n";

    foreach ($_POST as $key => $value) {
        // If the field exists in the $fields array, include it in the email
        if (isset($fields[$key])) {
            $emailText .= "$fields[$key]: $value\n";
        }
    }

    // All the neccessary headers for the email.
    $headers = array(
        'Content-Type: text/plain; charset="UTF-8";',
        'From: ' . $from,
        'Reply-To: ' . $from,
        'Return-Path: ' . $from,
    );

    // Send email
    mail($sendTo, $subject, $emailText, implode("\n", $headers));

    $responseArray = array('type' => 'success', 'message' => $okMessage);
} catch (\Exception $e) {
    $responseArray = array('type' => 'danger', 'message' => $errorMessage);
}


// Always return JSON response for better compatibility with chatbot
header('Content-Type: application/json');
echo json_encode($responseArray);
