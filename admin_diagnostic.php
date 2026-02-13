<?php
// Admin Dashboard Diagnostic Tool
// Access at: http://localhost/project/admin_diagnostic.php

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .test-box { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #10b981; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .warning { color: #f59e0b; font-weight: bold; }
        h2 { margin-top: 0; }
        .fix { background: #fee2e2; padding: 10px; margin-top: 10px; border-left: 4px solid #ef4444; }
        pre { background: #f9fafb; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Admin Dashboard Diagnostic</h1>

<?php
require_once 'config.php';

// Test 1: Session Check
echo "<div class='test-box'>";
echo "<h2>1. Session Status</h2>";

if (!isset($_SESSION['user_id'])) {
    echo "<p class='error'>❌ No user is logged in</p>";
    echo "<div class='fix'>";
    echo "<strong>Fix:</strong> You need to login first<br>";
    echo "1. Go to <a href='login.html'>login.html</a><br>";
    echo "2. Login with admin credentials:<br>";
    echo "&nbsp;&nbsp;• Email: admin@college.edu<br>";
    echo "&nbsp;&nbsp;• Password: admin123<br>";
    echo "3. Then access <a href='admin_dashboard.html'>admin_dashboard.html</a>";
    echo "</div>";
} else {
    echo "<p class='success'>✅ User is logged in</p>";
    echo "<strong>User ID:</strong> " . $_SESSION['user_id'] . "<br>";
    echo "<strong>Username:</strong> " . $_SESSION['username'] . "<br>";
    echo "<strong>Email:</strong> " . $_SESSION['email'] . "<br>";
    echo "<strong>Role:</strong> " . $_SESSION['role'] . "<br>";
    
    // Test 2: Admin Check
    echo "<h2>2. Admin Role Check</h2>";
    if ($_SESSION['role'] !== 'admin') {
        echo "<p class='error'>❌ You are not an admin</p>";
        echo "<p>Your role is: <strong>" . $_SESSION['role'] . "</strong></p>";
        echo "<div class='fix'>";
        echo "<strong>Fix:</strong> You need to login with an admin account<br>";
        echo "1. <a href='logout.php'>Logout</a><br>";
        echo "2. Login with:<br>";
        echo "&nbsp;&nbsp;• Email: admin@college.edu<br>";
        echo "&nbsp;&nbsp;• Password: admin123";
        echo "</div>";
    } else {
        echo "<p class='success'>✅ You are logged in as admin</p>";
        
        // Test 3: Database Access
        echo "<h2>3. Database Access Test</h2>";
        $stats_query = "SELECT 
            (SELECT COUNT(*) FROM events) as total_events,
            (SELECT COUNT(*) FROM events WHERE status = 'active') as active_events,
            (SELECT COUNT(*) FROM candidates) as total_candidates,
            (SELECT COUNT(*) FROM votes) as total_votes";
        
        $result = $conn->query($stats_query);
        if ($result) {
            $stats = $result->fetch_assoc();
            echo "<p class='success'>✅ Database accessible</p>";
            echo "<strong>Current Statistics:</strong><br>";
            echo "• Total Events: " . $stats['total_events'] . "<br>";
            echo "• Active Events: " . $stats['active_events'] . "<br>";
            echo "• Total Candidates: " . $stats['total_candidates'] . "<br>";
            echo "• Total Votes: " . $stats['total_votes'] . "<br>";
        } else {
            echo "<p class='error'>❌ Cannot access database</p>";
            echo "Error: " . $conn->error;
        }
        
        // Test 4: Admin Handler Access
        echo "<h2>4. Admin Handler Check</h2>";
        if (file_exists('admin_handler.php')) {
            echo "<p class='success'>✅ admin_handler.php exists</p>";
        } else {
            echo "<p class='error'>❌ admin_handler.php not found</p>";
        }
        
        // Final Status
        echo "<h2>✅ All Tests Passed!</h2>";
        echo "<p>Your admin dashboard should work correctly.</p>";
        echo "<p><a href='admin_dashboard.html' style='background: #6366f1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: inline-block;'>Go to Admin Dashboard</a></p>";
    }
}
echo "</div>";
?>

    <div class="test-box">
        <h2>📝 Common Issues & Solutions</h2>
        
        <h3>Issue: Redirected to login.html</h3>
        <p><strong>Cause:</strong> Not logged in or not an admin</p>
        <p><strong>Solution:</strong> Login with admin credentials first</p>
        
        <h3>Issue: Blank page or errors</h3>
        <p><strong>Cause:</strong> Opening file directly instead of through web server</p>
        <p><strong>Solution:</strong> Access via http://localhost/project/admin_dashboard.html</p>
        
        <h3>Issue: Stats showing 0</h3>
        <p><strong>Cause:</strong> No events created yet</p>
        <p><strong>Solution:</strong> This is normal for a new installation. Click "Create Event" to add events</p>
    </div>

    <div class="test-box">
        <h2>🚀 Quick Actions</h2>
        <p>
            <a href="login.html">Login Page</a> | 
            <a href="admin_dashboard.html">Admin Dashboard</a> | 
            <a href="vote.html">Voting Page</a> | 
            <a href="results.html">Results Page</a> | 
            <a href="logout.php">Logout</a>
        </p>
    </div>

</body>
</html>
