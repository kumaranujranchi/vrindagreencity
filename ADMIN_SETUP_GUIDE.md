# Vrinda Green City - Admin Panel Setup Guide

## Database Setup

### Step 1: Import the SQL File

1. Login to your hosting control panel (cPanel)
2. Go to **phpMyAdmin**
3. Select your database: `u743570205_vrindagreen`
4. Click on the **Import** tab
5. Choose the file: `database.sql`
6. Click **Go** to import

### Step 2: Database Configuration

The database configuration is already set in `admin/config.php`:

- **Host:** localhost
- **Database:** u743570205_vrindagreen
- **Username:** u743570205_vindagreen
- **Password:** Anuj@2025@2026

## Admin Panel Access

### Login URL

Access the admin panel at:

```
https://vrindagreencity.com/admin/login.php
```

### Default Admin Credentials

- **Username:** admin
- **Password:** Admin@123

⚠️ **IMPORTANT:** Change the default password after first login!

## Features

### 1. Dashboard (admin/index.php)

- Overview of all leads and statistics
- Quick stats cards showing:
  - Total contact leads
  - New leads count
  - Property inquiries
  - Newsletter subscribers
- Recent leads list

### 2. Contact Leads (admin/contact-leads.php)

- View all contact form submissions
- Filter by status (New, Contacted, Closed)
- Update lead status
- View full lead details
- Delete leads

### 3. Property Inquiries (admin/property-inquiries.php)

- View all property inquiry submissions
- Filter by status (New, Contacted, Interested, Closed)
- Update inquiry status
- Delete inquiries

### 4. Newsletter Subscribers (admin/subscribers.php)

- View all newsletter subscribers
- Filter by status (Active, Unsubscribed)
- Update subscriber status
- Export subscribers to CSV
- Delete subscribers

## Database Tables Created

### 1. admin_users

Stores admin user accounts with encrypted passwords.

### 2. contact_leads

Stores contact form submissions from the website.

### 3. property_inquiries

Stores property inquiry form submissions.

### 4. newsletter_subscribers

Stores newsletter subscription emails.

## Connecting Contact Form to Database

To save contact form submissions to the database, update your `inc/contact.php` file:

```php
<?php
require_once '../admin/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit;
    }

    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO contact_leads (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Thank you! We will contact you soon.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit. Please try again.']);
    }

    $stmt->close();
    closeDBConnection($conn);
}
?>
```

## Security Features

1. **Password Encryption:** All passwords are hashed using PHP's `password_hash()` function
2. **SQL Injection Protection:** Using prepared statements for all database queries
3. **XSS Protection:** All output is sanitized using `htmlspecialchars()`
4. **Session Management:** Secure session handling for admin authentication
5. **Access Control:** Login required for all admin pages

## File Structure

```
admin/
├── config.php              # Database configuration
├── login.php              # Admin login page
├── logout.php             # Logout handler
├── index.php              # Dashboard
├── header.php             # Common header
├── styles.css             # Admin panel styles
├── contact-leads.php      # Manage contact leads
├── property-inquiries.php # Manage property inquiries
├── subscribers.php        # Manage newsletter subscribers
├── view-lead.php          # View single lead details
├── export-subscribers.php # Export subscribers to CSV
└── .htaccess             # Security (if needed)

database.sql               # SQL file to create tables
```

## Creating Additional Admin Users

To create more admin users, run this SQL query in phpMyAdmin:

```sql
-- Replace 'newusername', 'newemail@example.com', and generate a new password hash
INSERT INTO admin_users (username, email, password)
VALUES ('newusername', 'newemail@example.com', '$2y$10$...');
```

To generate a password hash, create a temporary PHP file:

```php
<?php
echo password_hash('YourNewPassword', PASSWORD_DEFAULT);
?>
```

## Troubleshooting

### Can't Login

1. Make sure you imported the `database.sql` file
2. Check database credentials in `admin/config.php`
3. Verify the `admin_users` table exists

### Database Connection Error

1. Verify database credentials are correct
2. Make sure the database exists
3. Check if MySQL is running

### Permission Denied

1. Ensure proper file permissions (644 for files, 755 for directories)
2. Check server error logs

## Support

For any issues or questions, contact the development team.

---

**Last Updated:** November 19, 2025
