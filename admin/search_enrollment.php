<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_GET['term'])) {
    $searchTerm = trim($_GET['term']); // Trim whitespace
    
    try {
        $db = new Database();
        $conn = $db->connect();
        
        // Modified query to handle partial matches better
        $stmt = $conn->prepare("
            SELECT DISTINCT 
                ed.enrollment_number, 
                u.fullname, 
                u.user_id, 
                ed.verification_status,
                u.email
            FROM educational_details ed
            JOIN users u ON ed.user_id = u.user_id
            WHERE ed.enrollment_number LIKE ?
                OR ed.enrollment_number LIKE ?
                OR ed.enrollment_number LIKE ?
            ORDER BY 
                CASE 
                    WHEN ed.enrollment_number LIKE ? THEN 1
                    WHEN ed.enrollment_number LIKE ? THEN 2
                    ELSE 3
                END,
                ed.enrollment_number
            LIMIT 10
        ");
        
        // Search patterns
        $exactPattern = $searchTerm;
        $startPattern = $searchTerm . '%';
        $containsPattern = '%' . $searchTerm . '%';
        
        $stmt->execute([
            $exactPattern,
            $startPattern,
            $containsPattern,
            $exactPattern,
            $startPattern
        ]);
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format results for display
        $formattedResults = array_map(function($row) {
            return [
                'value' => $row['enrollment_number'],
                'label' => "{$row['enrollment_number']} - {$row['fullname']}",
                'user_id' => $row['user_id'],
                'verification_status' => $row['verification_status'],
                'email' => $row['email']
            ];
        }, $results);
        
        echo json_encode($formattedResults);
        
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
