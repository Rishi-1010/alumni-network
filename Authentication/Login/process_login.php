<?php
session_start();
require_once '../../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $db = new Database();
    $conn = $db->connect();

    if (!$conn) {
        $_SESSION['error'] = "Database connection failed.";
        header("Location: login.php");
        exit();
    }

    try {
        $result = $db->loginUser($email, $password);

        if ($result['status'] === 'success') {
            // Redirect to dashboard on successful login
            header("Location: ../../dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = $result['message'];
            header("Location: login.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error occurred: " . $e->getMessage();
        header("Location: login.php");
        exit();
    }
} 