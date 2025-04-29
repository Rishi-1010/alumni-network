<?php
session_start();
require_once '../../config/db_connection.php';

// Security check
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_POST['registration_id']) || !isset($_POST['status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit();
}

$db = new Database();
$conn = $db->connect();

try {
    $stmt = $conn->prepare("UPDATE event_registrations SET status = ? WHERE registration_id = ?");
    $stmt->execute([$_POST['status'], $_POST['registration_id']]);
    
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 