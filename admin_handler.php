<?php
require_once 'config.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'get_stats') {
        // Get dashboard statistics
        $stats = [];

        $result = $conn->query("SELECT COUNT(*) as count FROM events");
        $stats['total_events'] = $result->fetch_assoc()['count'];

        $result = $conn->query("SELECT COUNT(*) as count FROM events WHERE status = 'active'");
        $stats['active_events'] = $result->fetch_assoc()['count'];

        $result = $conn->query("SELECT COUNT(*) as count FROM candidates");
        $stats['total_candidates'] = $result->fetch_assoc()['count'];

        $result = $conn->query("SELECT COUNT(*) as count FROM votes");
        $stats['total_votes'] = $result->fetch_assoc()['count'];

        $response['success'] = true;
        $response['stats'] = $stats;
    } 
    elseif ($action === 'get_events') {
        $result = $conn->query("SELECT * FROM events ORDER BY created_at DESC");
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        $response['success'] = true;
        $response['events'] = $events;
    }
    elseif ($action === 'get_candidates') {
        $event_id = intval($_GET['event_id'] ?? 0);
        $stmt = $conn->prepare("SELECT * FROM candidates WHERE event_id = ? ORDER BY created_at ASC");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $candidates = [];
        while ($row = $result->fetch_assoc()) {
            $candidates[] = $row;
        }
        $response['success'] = true;
        $response['candidates'] = $candidates;
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_event') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $status = $_POST['status'] ?? 'upcoming';
        $created_by = $_SESSION['user_id'];

        if (empty($title) || empty($description) || empty($start_date) || empty($end_date)) {
            $response['message'] = 'All fields are required';
        } else {
            $stmt = $conn->prepare("INSERT INTO events (title, description, start_date, end_date, status, created_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $title, $description, $start_date, $end_date, $status, $created_by);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Event created successfully';
                $response['event_id'] = $stmt->insert_id;
            } else {
                $response['message'] = 'Failed to create event';
            }
            $stmt->close();
        }
    }
    elseif ($action === 'add_candidate') {
        $event_id = intval($_POST['event_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $photo = '';

        // Handle file upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $upload_dir = 'uploads/';
            
            // Create uploads directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_ext;
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
                $photo = $target_path;
            }
        }

        if (empty($name) || empty($description)) {
            $response['message'] = 'Name and description are required';
        } else {
            $stmt = $conn->prepare("INSERT INTO candidates (event_id, name, description, photo) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $event_id, $name, $description, $photo);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Candidate added successfully';
            } else {
                $response['message'] = 'Failed to add candidate';
            }
            $stmt->close();
        }
    }
    elseif ($action === 'delete_event') {
        $event_id = intval($_POST['event_id'] ?? 0);
        
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("i", $event_id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Event deleted successfully';
        } else {
            $response['message'] = 'Failed to delete event';
        }
        $stmt->close();
    }
    elseif ($action === 'delete_candidate') {
        $candidate_id = intval($_POST['candidate_id'] ?? 0);
        
        // Get photo path to delete file
        $stmt = $conn->prepare("SELECT photo FROM candidates WHERE id = ?");
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $candidate = $result->fetch_assoc();
        
        if ($candidate && !empty($candidate['photo']) && file_exists($candidate['photo'])) {
            unlink($candidate['photo']);
        }

        $stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
        $stmt->bind_param("i", $candidate_id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Candidate deleted successfully';
        } else {
            $response['message'] = 'Failed to delete candidate';
        }
        $stmt->close();
    }
}

echo json_encode($response);
$conn->close();
?>
