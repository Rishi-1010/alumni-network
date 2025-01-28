<?php
session_start();
require_once '../../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $db = new Database();
    $conn = $db->connect();

    try {
        // Check if connection is successful
        if (!$conn) {
            throw new PDOException("Database connection failed");
        }

        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        
        // Check if user exists
        if ($stmt->rowCount() === 0) {
            $_SESSION['error'] = "User not found";
            $_SESSION['debug'] = "User not found for username: $username";
            header("Location: login.php");
            exit();
        }

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug: Log fetched admin details
        $_SESSION['debug'] = "Fetched admin details: " . print_r($admin, true);

        // Verify password
        if (password_verify($password, $admin['password'])) {
            // Update last login
            $updateStmt = $conn->prepare("UPDATE admins SET last_login = CURRENT_TIMESTAMP WHERE admin_id = ?");
            $updateStmt->execute([$admin['admin_id']]);

            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            header("Location: ../../admin/dashboard.php");
            exit();
        } else {
            // Debug: Log password verification failure
            $_SESSION['debug'] = "Password verification failed for user: $username";
            $_SESSION['error'] = "Invalid password";
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        $_SESSION['debug'] = "PDOException: " . $e->getMessage();
        header("Location: login.php");
        exit();
    }
}
?>