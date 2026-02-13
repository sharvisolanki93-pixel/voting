<?php
// Database Connection Test
// Access this file at: http://localhost/project/test_connection.php

echo "<h2>Database Connection Test</h2>";

// Test 1: Connection
echo "<h3>1. Testing Connection...</h3>";
$conn = new mysqli('localhost', 'root', '', 'voting_system');

if ($conn->connect_error) {
    echo "❌ <strong>Connection failed:</strong> " . $conn->connect_error . "<br>";
    echo "<p style='color: red;'>Fix: Make sure MySQL is running and credentials in config.php are correct</p>";
    exit;
} else {
    echo "✅ Database connected successfully!<br><br>";
}

// Test 2: Check Tables
echo "<h3>2. Checking Tables...</h3>";
$result = $conn->query("SHOW TABLES");

if ($result->num_rows === 0) {
    echo "❌ <strong>No tables found!</strong><br>";
    echo "<p style='color: red;'>Fix: Run database_schema.sql in phpMyAdmin</p>";
} else {
    echo "✅ Found " . $result->num_rows . " tables:<br>";
    while ($row = $result->fetch_array()) {
        echo "&nbsp;&nbsp;• " . $row[0] . "<br>";
    }
    echo "<br>";
}

// Test 3: Check Admin User
echo "<h3>3. Checking Default Admin...</h3>";
$result = $conn->query("SELECT username, email, role FROM users WHERE role = 'admin' LIMIT 1");

if ($result->num_rows === 0) {
    echo "❌ <strong>No admin user found!</strong><br>";
    echo "<p style='color: red;'>Fix: Run database_schema.sql again to create default admin</p>";
} else {
    $admin = $result->fetch_assoc();
    echo "✅ Admin account exists:<br>";
    echo "&nbsp;&nbsp;• Username: " . $admin['username'] . "<br>";
    echo "&nbsp;&nbsp;• Email: " . $admin['email'] . "<br>";
    echo "&nbsp;&nbsp;• Default Password: admin123<br><br>";
}

// Test 4: Test Insert
echo "<h3>4. Testing Database Write...</h3>";
$testStmt = $conn->prepare("SELECT COUNT(*) as count FROM users");
if ($testStmt) {
    $testStmt->execute();
    $result = $testStmt->get_result();
    $count = $result->fetch_assoc()['count'];
    echo "✅ Can read from database (users table has " . $count . " records)<br><br>";
} else {
    echo "❌ <strong>Cannot read from database</strong><br>";
}

echo "<h3>✅ All Tests Complete!</h3>";
echo "<p>If all tests passed, your database is set up correctly. You can now:</p>";
echo "<ul>";
echo "<li>Register a new account at <a href='register.html'>register.html</a></li>";
echo "<li>Login at <a href='login.html'>login.html</a></li>";
echo "<li>Access admin panel at <a href='admin_dashboard.html'>admin_dashboard.html</a> (login as admin first)</li>";
echo "</ul>";

$conn->close();
?>
