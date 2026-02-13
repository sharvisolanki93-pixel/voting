<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'register';

    if ($action === 'register') {
        // Registration logic
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $response['message'] = 'All fields are required';
            echo json_encode($response);
            exit;
        }

        if (strlen($username) < 3) {
            $response['message'] = 'Username must be at least 3 characters';
            echo json_encode($response);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Invalid email format';
            echo json_encode($response);
            exit;
        }

        if (strlen($password) < 6) {
            $response['message'] = 'Password must be at least 6 characters';
            echo json_encode($response);
            exit;
        }

        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response['message'] = 'Username or email already exists';
            echo json_encode($response);
            exit;
        }

        // Hash password and insert user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, is_verified) VALUES (?, ?, ?, 'user', 1)");
        
        if (!$stmt) {
            $response['message'] = 'Database error: ' . $conn->error;
            echo json_encode($response);
            exit;
        }
        
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Registration successful! Please login.';
        } else {
            $response['message'] = 'Registration failed: ' . $stmt->error;
        }

        $stmt->close();
    } 
    elseif ($action === 'login') {
        // Login logic
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $response['message'] = 'Username and password are required';
            echo json_encode($response);
            exit;
        }

        // Check user credentials (allow login with username or email)
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                $response['success'] = true;
                $response['message'] = 'Login successful!';
                $response['role'] = $user['role'];
            } else {
                $response['message'] = 'Invalid password';
            }
        } else {
            $response['message'] = 'User not found';
        }

        $stmt->close();
    }
}

echo json_encode($response);
$conn->close();
?>
