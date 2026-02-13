<?php
// Admin Password Reset Tool
// Access at: http://localhost/project/reset_admin_password.php

require_once 'config.php';

echo "<h2>🔑 Admin Password Reset</h2>";
echo "<style>body { font-family: Arial, sans-serif; padding: 20px; } .success { color: #10b981; } .error { color: #ef4444; } .info { background: #e0e7ff; padding: 15px; border-radius: 8px; margin: 15px 0; }</style>";

// First, check if admin user exists
$result = $conn->query("SELECT id, username, email FROM users WHERE role = 'admin' LIMIT 1");

if ($result->num_rows === 0) {
    echo "<p class='error'>❌ No admin user found in database!</p>";
    echo "<p>Creating default admin user...</p>";
    
    // Create admin user with correct password hash
    $email = 'admin@college.edu';
    $username = 'admin';
    $password = 'admin123';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, is_verified) VALUES (?, ?, ?, 'admin', 1)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        echo "<p class='success'>✅ Admin user created successfully!</p>";
        echo "<div class='info'>";
        echo "<strong>Login Credentials:</strong><br>";
        echo "Email: <strong>admin@college.edu</strong><br>";
        echo "Password: <strong>admin123</strong><br>";
        echo "</div>";
    } else {
        echo "<p class='error'>❌ Failed to create admin user: " . $stmt->error . "</p>";
    }
    $stmt->close();
} else {
    $admin = $result->fetch_assoc();
    echo "<p class='success'>✅ Admin user exists</p>";
    echo "<p>Username: <strong>" . $admin['username'] . "</strong><br>";
    echo "Email: <strong>" . $admin['email'] . "</strong></p>";
    
    echo "<p>Resetting password to: <strong>admin123</strong></p>";
    
    // Reset password
    $password = 'admin123';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $admin['id']);
    
    if ($stmt->execute()) {
        echo "<p class='success'>✅ Password reset successfully!</p>";
        echo "<div class='info'>";
        echo "<strong>New Login Credentials:</strong><br>";
        echo "Email: <strong>" . $admin['email'] . "</strong><br>";
        echo "Password: <strong>admin123</strong><br>";
        echo "</div>";
    } else {
        echo "<p class='error'>❌ Failed to reset password: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

echo "<div class='info'>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Go to <a href='login.html'>Login Page</a></li>";
echo "<li>Use the credentials shown above</li>";
echo "<li>You should now be able to access the <a href='admin_dashboard.html'>Admin Dashboard</a></li>";
echo "</ol>";
echo "</div>";

// Verification
echo "<h3>🔍 Verification</h3>";
echo "<p>Let me verify the password hash is working...</p>";

$testPassword = 'admin123';
$result = $conn->query("SELECT password FROM users WHERE role = 'admin' LIMIT 1");
$admin = $result->fetch_assoc();

if (password_verify($testPassword, $admin['password'])) {
    echo "<p class='success'>✅ Password verification successful! Login should work now.</p>";
} else {
    echo "<p class='error'>❌ Password verification failed. There may be a deeper issue.</p>";
}

$conn->close();
?>

<div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-top: 20px;">
    <h3>⚠️ Security Note</h3>
    <p>After logging in successfully, you should:</p>
    <ol>
        <li>Delete this file (<code>reset_admin_password.php</code>) for security</li>
        <li>Change your admin password to something more secure</li>
    </ol>
</div>
