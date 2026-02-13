# Database Setup Instructions

## Problem: Registration Error

If you're seeing "An error occurred" when trying to register, the most common cause is that **the database hasn't been created yet**.

## Solution: Setup the Database

### Method 1: Using phpMyAdmin (Recommended)

1. **Open phpMyAdmin** in your browser
   - Usually at: `http://localhost/phpmyadmin`

2. **Click on "SQL" tab** at the top

3. **Copy and paste** the entire content from `database_schema.sql` file

4. **Click "Go"** to execute

5. **Verify** the database was created:
   - Look for "voting_system" in the left sidebar
   - It should have 4 tables: users, events, candidates, votes

### Method 2: Using MySQL Command Line

1. Open Command Prompt or Terminal

2. Login to MySQL:
   ```bash
   mysql -u root -p
   ```
   (Press Enter if there's no password)

3. Run the schema file:
   ```bash
   source d:/sharvi/project/database_schema.sql
   ```

4. Verify:
   ```sql
   SHOW DATABASES;
   USE voting_system;
   SHOW TABLES;
   ```

### Method 3: Manual Import in phpMyAdmin

1. Open phpMyAdmin
2. Click "New" in the left sidebar
3. Database name: `voting_system`
4. Click "Create"
5. Click "Import" tab
6. Choose file: `database_schema.sql`
7. Click "Go"

## After Setting Up Database

1. **Refresh** your registration page
2. **Try registering** again
3. You should now see a **specific error message** instead of generic one
4. If it still doesn't work, the new error message will tell you exactly what's wrong

## Common Issues

### Issue 1: "Connection failed: Access denied"
**Solution:** Update `config.php` with your MySQL credentials:
```php
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Issue 2: "Database error: Table 'voting_system.users' doesn't exist"
**Solution:** The database exists but tables weren't created. Run the SQL schema file again.

### Issue 3: "Network error: Unable to connect to server"
**Solution:** 
- Make sure Apache/XAMPP is running
- Check if you can access `http://localhost/phpmyadmin`
- Verify PHP files are in the correct directory (htdocs for XAMPP)

## Testing Database Connection

Create a file `test_connection.php` in your project folder:

```php
<?php
$conn = new mysqli('localhost', 'root', '', 'voting_system');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "✓ Database connected successfully!<br>";
    
    $result = $conn->query("SHOW TABLES");
    echo "Tables in database:<br>";
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "<br>";
    }
}
?>
```

Access it at: `http://localhost/project/test_connection.php`

If this shows "Database connected successfully!" and lists 4 tables, your database is set up correctly!

## Default Admin Account

After database setup, you can login with:
- **Email:** admin@college.edu
- **Password:** admin123

## Need More Help?

The error messages are now more detailed. Try registering again and check:
1. Browser console (F12) for JavaScript errors
2. The error message displayed on the page
3. PHP error logs if available
