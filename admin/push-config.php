<?php
/**
 * Push Notification Configuration
 * Store your VAPID keys here securely
 * 
 * IMPORTANT: Keep these keys secure and never commit them to public repositories
 */

// VAPID Keys for Web Push
// Generated VAPID keys - Keep the private key secure!
define('VAPID_PUBLIC_KEY', 'BB9GI9J5-oRxxrvXlHsmLeJ53rPPBYkKUmX0XMzDT3xLnzv2MZglhMZlljMvF6pHEqws8OLjvM5XpWXGbHueUsI');
define('VAPID_PRIVATE_KEY', 'THMlqdNQaS3uKuAfiMIlAUZct62bQC_r3j0cPHnc6c0');

// VAPID Subject (your contact email)
define('VAPID_SUBJECT', 'mailto:kumaranujranchi@gmail.com');

// Push Notification Settings
define('PUSH_ICON_DEFAULT', '/assets/img/logo.png');
define('PUSH_BADGE_DEFAULT', '/assets/img/badge.png');

// Notification Limits
define('MAX_NOTIFICATIONS_PER_DAY', 10); // Maximum notifications per subscriber per day
define('BATCH_SIZE', 100); // Number of notifications to send per batch

?>