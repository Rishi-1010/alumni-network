<?php
session_start();
require_once '../../config/db_connection.php';

// Security checks
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    die(json_encode(['error' => 'Unauthorized access']));
}

$db = new Database();
$conn = $db->connect();

try {
    // Count professional alumni (employed, seeking opportunities, further studies)
    $stmt = $conn->query("
        SELECT COUNT(DISTINCT u.user_id) AS professional_count 
        FROM users u
        JOIN professional_status ps ON u.user_id = ps.user_id
        WHERE ps.current_status IN ('employed', 'seeking', 'student')
    ");
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['count' => $result['professional_count']]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 