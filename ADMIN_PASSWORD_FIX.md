# 🔑 Admin Login Password Fix

## Problem
Getting "Invalid password" error when trying to login as admin with:
- Email: `admin@college.edu`
- Password: `admin123`

## Cause
The password hash in the database schema may not be compatible with your PHP version, or the admin user wasn't created properly.

## Solution (Choose ONE method)

---

### ⚡ Method 1: Automatic Fix (Recommended)

**Run the password reset tool:**

1. Open your browser
2. Go to: `http://localhost/project/reset_admin_password.php`
3. The tool will automatically:
   - Check if admin exists
   - Create admin if missing
   - Reset password to `admin123` with correct hash
   - Verify the fix worked

4. After it shows success, go to `login.html` and login with:
   - **Email:** `admin@college.edu`
   - **Password:** `admin123`

---

### 🔧 Method 2: Manual SQL Fix

If the automatic tool doesn't work:

1. **Open phpMyAdmin**: `http://localhost/phpmyadmin`

2. **Select the `voting_system` database** from the left sidebar

3. **Click "SQL" tab** at the top

4. **Copy and paste** this SQL:

```sql
-- Delete existing admin (if any)
DELETE FROM users WHERE email = 'admin@college.edu';

-- Create new admin with fresh password hash
INSERT INTO users (username, email, password, role, is_verified) VALUES
('admin', 'admin@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1);
```

5. **Click "Go"**

6. **Try logging in** again

---

### 🎯 Method 3: Create New Admin via Registration

If both above methods fail:

1. Create a **PHP script** to manually hash the password:

Create file `create_admin.php`:
```php
<?php
require_once 'config.php';

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: admin123<br>";
echo "Hash: $hash<br><br>";

// Insert admin
$stmt = $conn->prepare("DELETE FROM users WHERE email = 'admin@college.edu'");
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO users (username, email, password, role, is_verified) VALUES ('admin', 'admin@college.edu', ?, 'admin', 1)");
$stmt->bind_param("s", $hash);

if ($stmt->execute()) {
    echo "✅ Admin created successfully!<br>";
    echo "Login with: admin@college.edu / admin123";
} else {
    echo "❌ Error: " . $stmt->error;
}
?>
```

2. Access: `http://localhost/project/create_admin.php`
3. Login with the credentials shown

---

## Verification

After using any method above, verify it worked:

1. Go to: `http://localhost/project/admin_diagnostic.php`
2. It should show you're logged in as admin
3. Access: `http://localhost/project/admin_dashboard.html`

---

## Why This Happens

The password hash `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi` is pre-generated and may not work correctly on all PHP installations because:

- Different PHP versions may generate different hashes
- The hash needs to be created using `password_hash()` function
- The verification uses `password_verify()` which must match the creation method

The reset tool creates a fresh hash using your server's PHP version, which will definitely work.

---

## Quick Links

- [Password Reset Tool](http://localhost/project/reset_admin_password.php) ⚡ **Start Here**
- [Login Page](http://localhost/project/login.html)
- [Admin Diagnostic](http://localhost/project/admin_diagnostic.php)
- [Test Connection](http://localhost/project/test_connection.php)
