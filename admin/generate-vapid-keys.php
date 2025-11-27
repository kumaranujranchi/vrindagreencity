<?php
/**
 * VAPID Key Generator
 * Run this script once to generate your VAPID keys
 * 
 * Usage: php generate-vapid-keys.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Minishlink\WebPush\VAPID;

echo "==================================\n";
echo "  VAPID Key Generator\n";
echo "==================================\n\n";

try {
  $keys = VAPID::createVapidKeys();

  echo "✅ VAPID Keys Generated Successfully!\n\n";

  echo "📋 Public Key:\n";
  echo "-----------------------------------\n";
  echo $keys['publicKey'] . "\n\n";

  echo "🔐 Private Key:\n";
  echo "-----------------------------------\n";
  echo $keys['privateKey'] . "\n\n";

  echo "==================================\n";
  echo "⚠️  IMPORTANT INSTRUCTIONS\n";
  echo "==================================\n\n";

  echo "1. Copy these keys to admin/push-config.php:\n";
  echo "   define('VAPID_PUBLIC_KEY', '{$keys['publicKey']}');\n";
  echo "   define('VAPID_PRIVATE_KEY', '{$keys['privateKey']}');\n\n";

  echo "2. Copy the PUBLIC key to assets/js/push-notifications.js:\n";
  echo "   const VAPID_PUBLIC_KEY = '{$keys['publicKey']}';\n\n";

  echo "3. NEVER share your private key publicly!\n";
  echo "4. Keep these keys secure and backed up.\n\n";

  echo "✨ You're all set! Follow the rest of the setup guide.\n";

} catch (Exception $e) {
  echo "❌ Error: " . $e->getMessage() . "\n";
  echo "\nMake sure you've installed Composer dependencies:\n";
  echo "Run: composer install\n";
}
?>