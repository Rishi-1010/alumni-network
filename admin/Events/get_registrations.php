<?php
// Disable error display to prevent HTML errors in JSON response
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
require_once '../../config/db_connection.php';

// Set JSON content type header early
header('Content-Type: application/json');

// Security check
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['event_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Event ID is required']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->connect();

    // First check if the event exists
    $checkEvent = $conn->prepare("SELECT event_id FROM events WHERE event_id = ?");
    $checkEvent->execute([$_GET['event_id']]);
    if (!$checkEvent->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'Event not found']);
        exit();
    }

    // Get the registrations with user details - simplified query without status
    $stmt = $conn->prepare("
        SELECT 
            er.registration_id,
            er.registration_date,
            u.fullname,
            u.email,
            ed.enrollment_number
        FROM event_registrations er
        JOIN users u ON er.user_id = u.user_id
        LEFT JOIN educational_details ed ON u.user_id = ed.user_id
        WHERE er.event_id = ?
        ORDER BY er.registration_date DESC
    ");
    $stmt->execute([$_GET['event_id']]);
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the data for display - simplified without status
    $formattedRegistrations = array_map(function($reg) {
        return [
            'registration_id' => $reg['registration_id'],
            'name' => $reg['fullname'],
            'email' => $reg['email'],
            'enrollment_number' => $reg['enrollment_number'],
            'registration_date' => $reg['registration_date']
        ];
    }, $registrations);
    
    echo json_encode($formattedRegistrations ?: []);
} catch(PDOException $e) {
    error_log("Database error in get_registrations.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
}
?> 