<?php
// Debug endpoint to test contact.php directly
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Chatbot Submission</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .btn { background: #0D9B4D; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .result { margin-top: 20px; padding: 15px; border: 1px solid #ddd; }
        .success { background: #d4edda; }
        .error { background: #f8d7da; }
    </style>
</head>
<body>
    <h1>Debug: Test Chatbot Lead Submission</h1>
    
    <button class="btn" onclick="testSubmission()">Submit Test Lead via Chatbot Method</button>
    
    <div id="result"></div>
    
    <script>
        function testSubmission() {
            console.log("Starting test submission...");
            
            const formData = new FormData();
            formData.append("name", "Test Chatbot User");
            formData.append("email", "chatbot@test.com");
            formData.append("phone", "9999999999");
            formData.append("subject", "Test via Debug");
            formData.append("message", "This is a test submission from debug page");
            
            const apiPath = window.location.origin + "/inc/contact.php";
            console.log("Sending to:", apiPath);
            
            fetch(apiPath, {
                method: "POST",
                body: formData,
            })
            .then(response => {
                console.log("Response status:", response.status);
                return response.text();
            })
            .then(text => {
                console.log("Raw response:", text);
                
                try {
                    const data = JSON.parse(text);
                    document.getElementById('result').innerHTML = 
                        '<div class="result ' + (data.type === 'success' ? 'success' : 'error') + '">' +
                        '<h3>' + (data.type === 'success' ? '✅ Success' : '❌ Error') + '</h3>' +
                        '<p>' + data.message + '</p>' +
                        '<pre>' + JSON.stringify(data, null, 2) + '</pre>' +
                        '</div>';
                } catch (e) {
                    document.getElementById('result').innerHTML = 
                        '<div class="result error">' +
                        '<h3>❌ JSON Parse Error</h3>' +
                        '<p>Server response was not valid JSON</p>' +
                        '<pre>' + text + '</pre>' +
                        '</div>';
                }
            })
            .catch(error => {
                console.error("Error:", error);
                document.getElementById('result').innerHTML = 
                    '<div class="result error">' +
                    '<h3>❌ Network Error</h3>' +
                    '<p>' + error.message + '</p>' +
                    '</div>';
            });
        }
    </script>
    
    <hr>
    <h2>Instructions:</h2>
    <ol>
        <li>Click the button above to submit a test lead</li>
        <li>Check the result below</li>
        <li>Then check: <a href="/inc/test-chatbot-leads.php">View Leads</a></li>
        <li>If successful, lead should appear in database</li>
        <li>Check error log: <code>/admin/contact_form_errors.log</code></li>
    </ol>
</body>
</html>
