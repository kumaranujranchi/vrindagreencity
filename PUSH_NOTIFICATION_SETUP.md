# Push Notification Service - Setup Guide

This guide will help you set up the push notification service for Vrinda Green City website.

## Prerequisites

- PHP 7.4 or higher
- MySQL database
- Composer (PHP package manager)
- HTTPS-enabled website (required for production)
- Node.js (only for VAPID key generation)

## Step 1: Install Dependencies

### Install Composer

If you don't have Composer installed, download it from [getcomposer.org](https://getcomposer.org/)

### Install PHP Dependencies

Navigate to your project directory and run:

```bash
composer install
```

This will install the `minishlink/web-push` library required for sending push notifications.

## Step 2: Import Database Schema

Import the push notification tables into your database:

```bash
mysql -u your_username -p your_database_name < push_notification_schema.sql
```

Or use phpMyAdmin:
1. Open phpMyAdmin
2. Select your database
3. Go to "Import" tab
4. Choose `push_notification_schema.sql`
5. Click "Go"

## Step 3: Generate VAPID Keys

VAPID keys are required to authenticate your server with push services.

### Option 1: Using web-push-php Library

Create a file named `generate-vapid-keys.php` in your admin directory:

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Minishlink\WebPush\VAPID;

$keys = VAPID::createVapidKeys();

echo "Public Key:\n";
echo $keys['publicKey'] . "\n\n";
echo "Private Key:\n";
echo $keys['privateKey'] . "\n\n";

echo "Copy these keys to admin/push-config.php\n";
?>
```

Run it:
```bash
php admin/generate-vapid-keys.php
```

### Option 2: Using Node.js web-push

Install web-push globally:
```bash
npm install web-push -g
```

Generate keys:
```bash
web-push generate-vapid-keys
```

## Step 4: Configure VAPID Keys

Open `admin/push-config.php` and replace the placeholder values:

```php
define('VAPID_PUBLIC_KEY', 'YOUR_GENERATED_PUBLIC_KEY');
define('VAPID_PRIVATE_KEY', 'YOUR_GENERATED_PRIVATE_KEY');
define('VAPID_SUBJECT', 'mailto:admin@vrindagreencity.com'); // Your contact email
```

## Step 5: Update Frontend JavaScript

Open `assets/js/push-notifications.js` and replace the VAPID public key:

```javascript
const VAPID_PUBLIC_KEY = 'YOUR_GENERATED_PUBLIC_KEY'; // Same as in push-config.php
```

## Step 6: Add Widget to Your Website

Add the push notification widget to your homepage or any page where you want users to subscribe.

In `index.html`, add this code where you want the subscription widget to appear:

```php
<?php include 'inc/push-notification-widget.php'; ?>
```

## Step 7: Test the Service

### Test on Localhost (Chrome/Firefox)

1. Open your website on `https://localhost` or use a tool like ngrok to create HTTPS tunnel
2. You should see the push notification widget
3. Click "Subscribe" and allow notifications when prompted
4. Check the admin panel at `/admin/push-notifications.php` - you should see 1 subscriber
5. Send a test notification from the admin panel
6. You should receive the notification (even if you close the browser tab)

### Test Subscription Flow

1. Open browser developer console (F12)
2. Check for any errors in the Console tab
3. Go to Application > Service Workers - you should see `service-worker.js` registered
4. Go to Application > Push Messaging - you should see your subscription

### Test Notification Sending

1. Log into admin panel
2. Navigate to "Push Notifications"
3. Fill in the form:
   - Title: "Test Notification"
   - Message: "This is a test push notification"
   - URL: "/"
4. Click "Send to All Subscribers"
5. You should receive the notification immediately

## Troubleshooting

### Subscription Fails

**Problem**: "Failed to subscribe" error
**Solution**: 
- Ensure you're using HTTPS (not HTTP)
- Check that VAPID public key is correct in the JavaScript file
- Verify browser supports push notifications
- Check browser console for detailed errors

### Notifications Not Received

**Problem**: Subscription successful but no notifications received
**Solution**:
- Check if notification permission is granted (browser settings)
- Verify VAPID private key in `push-config.php` matches public key
- Check server PHP error logs
- Ensure Composer dependencies are installed correctly

### Database Errors

**Problem**: "Table doesn't exist" errors
**Solution**:
- Verify you imported `push_notification_schema.sql`
- Check table names in your database
- Ensure database credentials in `admin/config.php` are correct

### Service Worker Not Registering

**Problem**: Service worker fails to register
**Solution**:
- Ensure `service-worker.js` is in the root directory
- Check file permissions (should be readable)
- Verify no syntax errors in service-worker.js
- Clear browser cache and try again

## Production Deployment

Before deploying to production:

1. ✅ **HTTPS is mandatory** - Push notifications only work over HTTPS
2. ✅ Keep VAPID private key secure - never commit to version control
3. ✅ Test thoroughly on staging environment first
4. ✅ Monitor subscriber growth and notification delivery rates
5. ✅ Set up proper error logging

## Security Notes

- Never expose VAPID private key publicly
- Store keys in environment variables in production
- Implement rate limiting for notification sending
- Validate and sanitize all notification content
- Consider implementing user preferences for notification frequency

## Browser Support

- Chrome/Edge: ✅ Full support
- Firefox: ✅ Full support
- Safari: ✅ Full support (iOS 16.4+)
- Opera: ✅ Full support

## Maintenance

### Clean Up Expired Subscriptions

Expired subscriptions are automatically removed when notifications fail with a 410 status. You can also manually clean them up:

```sql
DELETE FROM push_subscribers WHERE last_active < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### Monitor Database Growth

Push notification logs can grow quickly. Consider archiving old logs:

```sql
DELETE FROM push_notification_logs WHERE sent_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

## Support

For issues or questions:
- Check browser console for JavaScript errors
- Review PHP error logs
- Verify all configuration settings
- Test with different browsers

## Next Steps

- Customize the notification widget design to match your brand
- Add more notification actions in the service worker
- Implement scheduled notifications
- Create user preference settings for notification types
- Add analytics to track engagement rates

---

**Congratulations!** Your push notification service is now set up and ready to use. 🎉
