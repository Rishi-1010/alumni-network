<?php
session_start();
require_once '../../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $db = new Database();
    $conn = $db->connect();

    try {
        // Debug: Check if connection is successful
        if (!$conn) {
            throw new PDOException("Database connection failed");
        }

        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->execute([$username]);
        
        // Debug: Check if user exists
        if ($stmt->rowCount() === 0) {
            $_SESSION['error'] = "User not found";
            header("Location: login.php");
            exit();
        }

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug: Print password verification details
        if (password_verify($password, $admin['password'])) {
            // Update last login
            $updateStmt = $conn->prepare("UPDATE admin SET last_login = CURRENT_TIMESTAMP WHERE admin_id = ?");
            $updateStmt->execute([$admin['admin_id']]);

            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            header("Location: ../../admin/dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password";
            header("Location: login.php");
            exit();
        }

    } catch(PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: login.php");
        exit();
    }
} 