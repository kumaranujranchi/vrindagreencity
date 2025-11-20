# Database Configuration Issue - Solution Steps

## Problem
Access denied error means either:
1. Database password is wrong
2. Database user doesn't have access to the database
3. Database user doesn't exist

## Fix in Hostinger:

### Step 1: Check Database Credentials
1. Login to **Hostinger hPanel**
2. Go to **Databases → MySQL Databases**
3. Check:
   - Database name: `u743570205_vrindagreen`
   - User name: `u743570205_vrindagreen`
   - Password: Check if it's `Anuj@2025@2026` or different

### Step 2: If Password is Different
1. Either **change password** to match config.php: `Anuj@2025@2026`
2. Or **update config.php** with correct password

### Step 3: Check User Permissions
1. In MySQL Databases section
2. Find "Add User to Database" section
3. Make sure user `u743570205_vrindagreen` is linked to database `u743570205_vrindagreen`
4. User should have **ALL PRIVILEGES**

### Step 4: Reset Password (Recommended)
1. In Hostinger hPanel → MySQL Databases
2. Find the user `u743570205_vrindagreen`
3. Click "Change Password"
4. Set new password (write it down!)
5. Update config.php with new password
6. Deploy again

## Alternative: Create New Database User
If nothing works, create fresh:
1. Create new database user with a simple password
2. Link user to database with ALL PRIVILEGES
3. Update config.php with new credentials
4. Deploy

## Test After Fix
After fixing, visit: https://vrindagreencity.com/test-db.php
Should show "Connected Successfully"
