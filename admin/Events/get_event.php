<?php
session_start();
require_once '../../config/db_connection.php';

// Security checks
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Event ID is required']);
    exit();
}

$db = new Database();
$conn = $db->connect();

try {
    $event_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        http_response_code(404);
        echo json_encode(['error' => 'Event not found']);
        exit();
    }

    header('Content-Type: application/json');
    echo json_encode($event);
} catch(PDOException $e) {
    error_log("Database error in get_event.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
}
?> 