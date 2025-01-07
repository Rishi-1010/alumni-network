<?php
session_start();
require_once '../config/db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    
    $db = new Database();
    $conn = $db->connect();
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Delete from professional_status
        $stmt = $conn->prepare("DELETE FROM professional_status WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Delete from educational_details
        $stmt = $conn->prepare("DELETE FROM educational_details WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Finally delete from users table
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully']);
        
    } catch(PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Error deleting record: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
} 