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

        $stmt = $conn->prepare("SELECT * FROM companies WHERE username = ?");
        $stmt->execute([$username]);
        
        // Check if user exists
        if ($stmt->rowCount() === 0) {
            $_SESSION['error'] = "Company not found";
            $_SESSION['debug'] = "Company not found for username: $username";
            header("Location: login.php");
            exit();
        }

        $company = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug: Log fetched company details
        $_SESSION['debug'] = "Fetched company details: " . print_r($company, true);

        // Verify password
        if (password_verify($password, $company['password'])) {
            // Update last login
            $updateStmt = $conn->prepare("UPDATE companies SET last_login = CURRENT_TIMESTAMP WHERE company_id = ?");
            $updateStmt->execute([$company['company_id']]);

            $_SESSION['company_id'] = $company['company_id'];
            $_SESSION['company_username'] = $company['username'];
            
            header("Location: ../../company/dashboard.php"); // Redirect to company dashboard
            exit();
        } else {
            // Debug: Log password verification failure
            $_SESSION['debug'] = "Password verification failed for company: $username";
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
