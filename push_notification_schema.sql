-- Push Notification Service Database Schema
-- Add these tables to your existing database: u743570205_vrindagreen

-- Table for storing push notification subscribers
CREATE TABLE IF NOT EXISTS `push_subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `endpoint` varchar(500) NOT NULL,
  `p256dh_key` varchar(255) NOT NULL,
  `auth_token` varchar(255) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `subscribed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_active` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `endpoint` (`endpoint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for storing notification content and metadata
CREATE TABLE IF NOT EXISTS `push_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `icon` varchar(500) DEFAULT NULL,
  `badge` varchar(500) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `status` enum('draft','scheduled','sent','failed') DEFAULT 'draft',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_sent` int(11) DEFAULT 0,
  `total_failed` int(11) DEFAULT 0,
  `total_success` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for logging individual notification deliveries
CREATE TABLE IF NOT EXISTS `push_notification_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `status` enum('success','failed','expired') DEFAULT 'failed',
  `response` text DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `notification_id` (`notification_id`),
  KEY `subscriber_id` (`subscriber_id`),
  CONSTRAINT `fk_logs_notification` FOREIGN KEY (`notification_id`) REFERENCES `push_notifications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_logs_subscriber` FOREIGN KEY (`subscriber_id`) REFERENCES `push_subscribers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert a sample notification (optional - for testing)
-- You can delete this after initial setup
INSERT INTO `push_notifications` (`title`, `body`, `icon`, `url`, `status`) VALUES
('Welcome to Vrinda Green City', 'Thank you for subscribing to our notifications! Stay updated with latest property updates.', '/assets/img/logo.png', '/', 'draft');
