<?php
// Session Debug Tool
// Access at: http://localhost/project/check_session.php

require_once 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Debug</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #10b981; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        pre { background: #f9fafb; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .fix { background: #fef3c7; padding: 15px; margin: 10px 0; border-left: 4px solid #f59e0b; }
    </style>
</head>
<body>
    <h1>🔍 Session Debug Tool</h1>

    <div class="box">
        <h2>Current Session Status</h2>
        <?php
        echo "<pre>";
        echo "Session ID: " . session_id() . "\n";
        echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Inactive") . "\n\n";
        
        echo "Session Data:\n";
        if (empty($_SESSION)) {
            echo "  ❌ No session data (not logged in)\n";
        } else {
            print_r($_SESSION);
        }
        echo "</pre>";
        ?>
    </div>

    <div class="box">
        <h2>Authentication Check</h2>
        <?php
        if (!isset($_SESSION['user_id'])) {
            echo "<p class='error'>❌ Not logged in</p>";
            echo "<div class='fix'>";
            echo "<strong>FIX:</strong><br>";
            echo "1. Go to <a href='login.html'>login.html</a><br>";
            echo "2. Login with admin credentials:<br>";
            echo "&nbsp;&nbsp;• Email: admin@college.edu<br>";
            echo "&nbsp;&nbsp;• Password: admin123<br>";
            echo "3. Then try the admin dashboard again";
            echo "</div>";
        } else {
            echo "<p class='success'>✅ Logged in</p>";
            echo "<strong>User ID:</strong> " . $_SESSION['user_id'] . "<br>";
            echo "<strong>Username:</strong> " . $_SESSION['username'] . "<br>";
            echo "<strong>Email:</strong> " . $_SESSION['email'] . "<br>";
            echo "<strong>Role:</strong> " . $_SESSION['role'] . "<br><br>";
            
            if ($_SESSION['role'] === 'admin') {
                echo "<p class='success'>✅ Admin role confirmed</p>";
                echo "<p>You should be able to use admin dashboard now!</p>";
                echo "<p><a href='admin_dashboard.html' style='background: #6366f1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block;'>Go to Admin Dashboard</a></p>";
            } else {
                echo "<p class='error'>❌ You are not an admin (role: " . $_SESSION['role'] . ")</p>";
                echo "<div class='fix'>";
                echo "<strong>FIX:</strong><br>";
                echo "1. <a href='logout.php'>Logout</a> from current account<br>";
                echo "2. Login with admin account:<br>";
                echo "&nbsp;&nbsp;• Email: admin@college.edu<br>";
                echo "&nbsp;&nbsp;• Password: admin123";
                echo "</div>";
            }
        }
        ?>
    </div>

    <div class="box">
        <h2>Database Check</h2>
        <?php
        $result = $conn->query("SELECT id, username, email, role FROM users WHERE role = 'admin'");
        if ($result->num_rows > 0) {
            echo "<p class='success'>✅ Admin user exists in database</p>";
            while ($row = $result->fetch_assoc()) {
                echo "• " . $row['email'] . " (username: " . $row['username'] . ")<br>";
            }
        } else {
            echo "<p class='error'>❌ No admin user in database</p>";
            echo "<p>Run <a href='reset_admin_password.php'>reset_admin_password.php</a> to create admin user</p>";
        }
        ?>
    </div>

    <div class="box">
        <h2>Common Issues</h2>
        
        <h3>Issue 1: Session Expired</h3>
        <p><strong>Symptom:</strong> "Error: Unauthorized" when adding candidates</p>
        <p><strong>Cause:</strong> Your session expired (browser was idle too long)</p>
        <p><strong>Fix:</strong> Login again at <a href='login.html'>login.html</a></p>
        
        <h3>Issue 2: Logged in as Regular User</h3>
        <p><strong>Symptom:</strong> "Error: Unauthorized"</p>
        <p><strong>Cause:</strong> You registered a new account instead of using admin login</p>
        <p><strong>Fix:</strong> Logout and login with admin@college.edu</p>
        
        <h3>Issue 3: Admin Password Not Working</h3>
        <p><strong>Symptom:</strong> "Invalid password" when trying to login as admin</p>
        <p><strong>Fix:</strong> Run <a href='reset_admin_password.php'>reset_admin_password.php</a></p>
    </div>

    <div class="box">
        <h2>Quick Actions</h2>
        <p>
            <a href="login.html">Login</a> | 
            <a href="logout.php">Logout</a> | 
            <a href="admin_dashboard.html">Admin Dashboard</a> | 
            <a href="reset_admin_password.php">Reset Admin Password</a> |
            <a href="test_connection.php">Test Database</a>
        </p>
    </div>

</body>
</html>
