<?php
// File: C:\xampp\htdocs\alumni-network\admin\Alumni Management\delete_alumni.php

session_start();
require_once '../../config/db_connection.php';

// Define the delete directory function directly in this file
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }

    return rmdir($dir);
}

// Set header to JSON
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Admin not logged in']);
    exit();
}

if (!isset($_POST['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'No user specified for deletion']);
    exit();
}

$userId = $_POST['user_id'];

try {
    $db = new Database();
    $conn = $db->connect();
    
    // Start transaction
    $conn->beginTransaction();

    try {
        // First, get the enrollment number
        $stmt = $conn->prepare("
            SELECT ed.enrollment_number 
            FROM educational_details ed 
            WHERE ed.user_id = ?
        ");
        $stmt->execute([$userId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData && $userData['enrollment_number']) {
            // Define the certificate directory path
            $certificateDir = dirname(dirname(__DIR__)) . '/assets/certificates/' . $userData['enrollment_number'];
            
            // Log the path for debugging
            error_log("Attempting to delete directory: " . $certificateDir);

            // Delete the certificate directory and all its contents
            if (file_exists($certificateDir)) {
                if (!deleteDirectory($certificateDir)) {
                    throw new Exception("Failed to delete certificate directory");
                }
            }
        }

        // Now delete the user from the database
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);

        // If we get here, everything worked
        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Alumni and all related data deleted successfully'
        ]);

    } catch (Exception $e) {
        // Rollback the transaction if anything fails
        $conn->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    error_log("Error deleting alumni: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error deleting alumni: ' . $e->getMessage()
    ]);
}
?>
