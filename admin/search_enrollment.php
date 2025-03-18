<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_GET['term'])) {
    $searchTerm = $_GET['term'];
    
    try {
        $db = new Database();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("
            SELECT DISTINCT ed.enrollment_number, u.fullname
            FROM educational_details ed
            JOIN users u ON ed.user_id = u.user_id
            WHERE ed.enrollment_number LIKE ?
            LIMIT 10
        ");
        
        $stmt->execute(["%$searchTerm%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($results);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?> 