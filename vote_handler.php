<?php
require_once 'config.php';

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$response = ['success' => false, 'message' => ''];
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'get_active_events') {
        // Get active events with candidates
        $events_query = "SELECT * FROM events WHERE status = 'active' AND 
                        start_date <= NOW() AND end_date >= NOW() 
                        ORDER BY start_date ASC";
        $events_result = $conn->query($events_query);

        $events = [];
        while ($event = $events_result->fetch_assoc()) {
            // Get candidates for this event
            $event_id = $event['id'];
            $candidates_stmt = $conn->prepare("SELECT c.*, 
                    (SELECT COUNT(*) FROM votes v WHERE v.candidate_id = c.id AND v.user_id = ?) as user_voted
                    FROM candidates c 
                    WHERE c.event_id = ? 
                    ORDER BY c.created_at ASC");
            $candidates_stmt->bind_param("ii", $user_id, $event_id);
            $candidates_stmt->execute();
            $candidates_result = $candidates_stmt->get_result();

            $candidates = [];
            while ($candidate = $candidates_result->fetch_assoc()) {
                $candidates[] = $candidate;
            }

            $event['candidates'] = $candidates;
            $events[] = $event;
            $candidates_stmt->close();
        }

        // Get events user has already voted in
        $votes_stmt = $conn->prepare("SELECT DISTINCT event_id FROM votes WHERE user_id = ?");
        $votes_stmt->bind_param("i", $user_id);
        $votes_stmt->execute();
        $votes_result = $votes_stmt->get_result();

        $user_votes = [];
        while ($vote = $votes_result->fetch_assoc()) {
            $user_votes[] = $vote['event_id'];
        }

        $response['success'] = true;
        $response['events'] = $events;
        $response['user_votes'] = $user_votes;
        $votes_stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'cast_vote') {
        $event_id = intval($_POST['event_id'] ?? 0);
        $candidate_id = intval($_POST['candidate_id'] ?? 0);

        // Check if event is active
        $stmt = $conn->prepare("SELECT * FROM events WHERE id = ? AND status = 'active' AND 
                               start_date <= NOW() AND end_date >= NOW()");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $response['message'] = 'Event is not active or does not exist';
            echo json_encode($response);
            exit;
        }
        $stmt->close();

        // Check if candidate belongs to this event
        $stmt = $conn->prepare("SELECT * FROM candidates WHERE id = ? AND event_id = ?");
        $stmt->bind_param("ii", $candidate_id, $event_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $response['message'] = 'Invalid candidate selection';
            echo json_encode($response);
            exit;
        }
        $stmt->close();

        // Check if user has already voted in this event
        $stmt = $conn->prepare("SELECT * FROM votes WHERE event_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $event_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response['message'] = 'You have already voted in this event';
            echo json_encode($response);
            exit;
        }
        $stmt->close();

        // Cast the vote
        $stmt = $conn->prepare("INSERT INTO votes (event_id, candidate_id, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $event_id, $candidate_id, $user_id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Vote cast successfully';
        } else {
            $response['message'] = 'Failed to cast vote';
        }
        $stmt->close();
    }
}

echo json_encode($response);
$conn->close();
?>
