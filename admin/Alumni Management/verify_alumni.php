<?php
session_start();
require_once '../../config/db_connection.php';

// Set content type to JSON for AJAX responses
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    try {
        $db = new Database();
        $conn = $db->connect();

        // Update verification status
        $stmt = $conn->prepare("UPDATE educational_details SET verification_status = 'verified' WHERE user_id = ?");
        $result = $stmt->execute([$userId]);
        
        if ($result) {
            // Check if this is an AJAX request
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                // Return JSON response for AJAX requests
                echo json_encode(['success' => true, 'message' => 'Alumni verified successfully']);
            } else {
                // Redirect for regular form submissions
                $_SESSION['success'] = "Alumni verified successfully";
                header("Location: totalalumnis.php");
            }
        } else {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['error' => 'Failed to verify alumni']);
            } else {
                $_SESSION['error'] = "Failed to verify alumni";
                header("Location: totalalumnis.php");
            }
        }
    } catch (PDOException $e) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        } else {
            die("Error: " . $e->getMessage());
        }
    }
} else {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['error' => 'No user ID provided']);
    } else {
        $_SESSION['error'] = "No user ID provided";
        header("Location: totalalumnis.php");
    }
}
?>
