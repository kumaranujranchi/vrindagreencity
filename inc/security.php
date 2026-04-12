<?php
/**
 * Security Engine for Vrinda Green City
 * Handles Rate Limiting, CSRF, Honeypots, and Bot Detection
 */

class Security {
    private static $storageDir = __DIR__ . '/../admin/security/';
    private static $logFile = __DIR__ . '/../admin/security/attacks.log';
    private static $rateLimitPeriod = 3600; // 1 hour
    private static $maxSubmissions = 3;    // Per hour
    private static $honeypotField = 'website_url'; // Invisible field name

    public static function init() {
        if (!file_exists(self::$storageDir)) {
            @mkdir(self::$storageDir, 0755, true);
        }

        // Basic session security
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => isset($_SERVER['HTTPS']),
                'use_strict_mode' => true
            ]);
        }

        // Check if IP is blacklisted (manual block)
        if (self::isIPBlacklisted(self::getIP())) {
            self::logBlockedRequest('Blacklisted IP');
            http_response_code(403);
            die("Access Denied: Your IP has been flagged for suspicious activity.");
        }

        // Global bot check
        if (self::isSuspiciousBot()) {
            self::logBlockedRequest('Suspicious Bot Pattern');
            // We dont always block bots site-wide, but we can restrict them
        }
    }

    /**
     * Get real client IP, considering Cloudflare
     */
    public static function getIP() {
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Check rate limiting for a specific action (e.g. 'contact_form')
     */
    public static function checkRateLimit($action, $limit = null) {
        $ip = self::getIP();
        $hash = md5($ip . $action);
        $file = self::$storageDir . 'rl_' . $hash . '.json';

        $limit = $limit ?? self::$maxSubmissions;
        $now = time();
        $data = ['count' => 0, 'first_time' => $now];

        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if ($now - $data['first_time'] > self::$rateLimitPeriod) {
                // Reset limit
                $data = ['count' => 0, 'first_time' => $now];
            }
        }

        $data['count']++;
        file_put_contents($file, json_encode($data));

        if ($data['count'] > $limit) {
            self::logBlockedRequest("Rate Limit Exceeded: $action (Limit: $limit)");
            return false;
        }

        return true;
    }

    /**
     * CSRF Protection - Generate Token
     */
    public static function getCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * CSRF Protection - Verify Token
     */
    public static function verifyCSRFToken($token = null) {
        if ($token === null) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        }
        
        if (empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            self::logBlockedRequest('CSRF Validation Failed');
            return false;
        }
        return true;
    }

    /**
     * Honeypot - Check if invisible field was filled
     */
    public static function verifyHoneypot() {
        if (!empty($_POST[self::$honeypotField])) {
            self::logBlockedRequest('Honeypot Triggered');
            return false;
        }
        return true;
    }

    /**
     * Generate HTML for security fields
     */
    public static function renderSecurityFields() {
        $token = self::getCSRFToken();
        $field = self::$honeypotField;
        return '
            <input type="hidden" name="csrf_token" value="' . $token . '">
            <div style="display:none !important;">
                <label>Do not fill this field if you are human</label>
                <input type="text" name="' . $field . '" autocomplete="off" tabindex="-1">
            </div>
        ';
    }

    /**
     * Log suspicious activity
     */
    public static function logBlockedRequest($reason) {
        $entry = sprintf("[%s] IP: %s | UA: %s | Reason: %s | URI: %s\n",
            date('Y-m-d H:i:s'),
            self::getIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            $reason,
            $_SERVER['REQUEST_URI']
        );
        @file_put_contents(self::$logFile, $entry, FILE_APPEND);
    }

    /**
     * Check if IP is blacklisted
     */
    private static function isIPBlacklisted($ip) {
        $blacklistFile = self::$storageDir . 'blacklist.txt';
        if (!file_exists($blacklistFile)) return false;
        
        $ips = file($blacklistFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return in_array($ip, $ips);
    }

    /**
     * Detect suspicious bot patterns
     */
    private static function isSuspiciousBot() {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $badUAs = [
            'curl', 'python', 'Go-http-client', 'HeadlessChrome', 'Java/', 'libwww',
            'wget', 'Scanner', 'pider', 'bot', 'Bot', 'Crawler', 'Slurp'
        ];
        
        // Exclude common safe bots if needed (like Googlebot)
        $safeBots = ['Googlebot', 'bingbot', 'Baiduspider'];
        
        foreach ($safeBots as $safe) {
            if (stripos($ua, $safe) !== false) return false;
        }

        foreach ($badUAs as $bad) {
            if (stripos($ua, $bad) !== false) return true;
        }

        return false;
    }
}
