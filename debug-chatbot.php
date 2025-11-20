<?php
// Debug endpoint to test contact.php directly
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Chatbot Submission</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        .btn { background: #0D9B4D; color: white; padding: 12px 24px; border: none; cursor: pointer; border-radius: 5px; font-size: 16px; }
        .btn:hover { background: #0a7d3d; }
        .result { margin-top: 20px; padding: 20px; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
        h1 { color: #0D9B4D; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 Chatbot Lead Submission Tester</h1>
        
        <div class="info">
            <p><strong>This page tests the NEW chatbot endpoint:</strong> <code>/inc/chatbot-lead.php</code></p>
        </div>
        
        <button class="btn" onclick="testSubmission()">📤 Submit Test Lead</button>
        
        <div id="result"></div>
        
        <script>
            function testSubmission() {
                console.log("Starting test submission...");
                
                document.getElementById('result').innerHTML = '<div class="result">⏳ Submitting...</div>';
                
                const formData = new FormData();
                formData.append("name", "Test User " + Date.now());
                formData.append("email", "test" + Date.now() + "@chatbot.com");
                formData.append("phone", "9876543210");
                formData.append("subject", "Test Chatbot Lead");
                formData.append("message", "This is a test submission from debug page at " + new Date().toLocaleString());
                
                const apiPath = window.location.origin + "/inc/chatbot-lead.php";
                console.log("Sending to:", apiPath);
                
                fetch(apiPath, {
                    method: "POST",
                    body: formData,
                })
                .then(response => {
                    console.log("Response status:", response.status);
                    console.log("Response headers:", response.headers);
                    return response.text();
                })
                .then(text => {
                    console.log("Raw response:", text);
                    
                    try {
                        const data = JSON.parse(text);
                        console.log("Parsed data:", data);
                        
                        if (data.type === 'success') {
                            document.getElementById('result').innerHTML = 
                                '<div class="result success">' +
                                '<h3>✅ SUCCESS!</h3>' +
                                '<p><strong>Message:</strong> ' + data.message + '</p>' +
                                '<p><strong>Lead ID:</strong> ' + data.lead_id + '</p>' +
                                '<p><strong>Saved Data:</strong></p>' +
                                '<pre>' + JSON.stringify(data.data, null, 2) + '</pre>' +
                                '<p>✅ Lead has been saved to database!</p>' +
                                '<p><a href="/inc/test-chatbot-leads.php" target="_blank">View All Leads →</a></p>' +
                                '</div>';
                        } else {
                            document.getElementById('result').innerHTML = 
                                '<div class="result error">' +
                                '<h3>❌ Error</h3>' +
                                '<p><strong>Message:</strong> ' + data.message + '</p>' +
                                (data.error ? '<p><strong>Details:</strong> ' + data.error + '</p>' : '') +
                                '<pre>' + JSON.stringify(data, null, 2) + '</pre>' +
                                '</div>';
                        }
                    } catch (e) {
                        console.error("JSON parse error:", e);
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
        
        <hr style="margin: 30px 0;">
        
        <h2>📋 Instructions:</h2>
        <ol>
            <li>Click the <strong>"Submit Test Lead"</strong> button above</li>
            <li>Check the result - should show success with Lead ID</li>
            <li>Click <strong>"View All Leads"</strong> link to verify in database</li>
            <li>If successful here, test actual chatbot on homepage</li>
            <li>Check error logs at: <code>/admin/chatbot_leads.log</code></li>
        </ol>
        
        <h2>🔗 Quick Links:</h2>
        <ul>
            <li><a href="/inc/test-chatbot-leads.php" target="_blank">View All Leads</a></li>
            <li><a href="/admin/contact-leads.php" target="_blank">Admin Dashboard - Contact Leads</a></li>
            <li><a href="/" target="_blank">Homepage (Test Real Chatbot)</a></li>
        </ul>
    </div>
</body>
</html>
