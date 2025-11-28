<?php
// CLI test script to simulate a chatbot POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['name'] = 'Test User';
$_POST['email'] = 'testuser@example.com';
$_POST['phone'] = '9999999999';
$_POST['subject'] = 'Test Subject';
$_POST['message'] = 'This is a test lead from CLI script.';

require_once __DIR__ . '/../inc/chatbot-lead.php';
echo "
Done.
";