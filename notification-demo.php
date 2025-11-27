<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vrinda Green City - Push Notifications Demo</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .container {
      max-width: 800px;
      width: 100%;
    }

    .header {
      text-align: center;
      color: white;
      margin-bottom: 40px;
    }

    .header h1 {
      font-size: 36px;
      margin-bottom: 10px;
    }

    .header p {
      font-size: 18px;
      opacity: 0.9;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h1>Welcome to Vrinda Green City</h1>
      <p>Stay updated with the latest property news and offers</p>
    </div>

    <?php include 'inc/push-notification-widget.php'; ?>
  </div>
</body>

</html>