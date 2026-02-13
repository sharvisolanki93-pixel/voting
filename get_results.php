<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

// Get all events with their results
$events_query = "SELECT * FROM events ORDER BY 
                CASE status 
                    WHEN 'active' THEN 1 
                    WHEN 'closed' THEN 2 
                    WHEN 'upcoming' THEN 3 
                END, 
                start_date DESC";
$events_result = $conn->query($events_query);

$events = [];
while ($event = $events_result->fetch_assoc()) {
    $event_id = $event['id'];

    // Get candidates with vote counts
    $results_query = "SELECT c.id, c.name, c.description, c.photo, 
                     COUNT(v.id) as vote_count
                     FROM candidates c
                     LEFT JOIN votes v ON c.id = v.candidate_id
                     WHERE c.event_id = ?
                     GROUP BY c.id
                     ORDER BY vote_count DESC, c.name ASC";
    
    $stmt = $conn->prepare($results_query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $results = $stmt->get_result();

    $event_results = [];
    while ($result = $results->fetch_assoc()) {
        $event_results[] = $result;
    }

    $event['results'] = $event_results;
    $events[] = $event;
    $stmt->close();
}

$response['success'] = true;
$response['events'] = $events;

echo json_encode($response);
$conn->close();
?>
