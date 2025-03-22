<?php
session_start();
require_once '../config/db_connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../Authentication/AdminLogin/login.php");
    exit();
}

if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    try {
        $db = new Database();
        $conn = $db->connect();

        $stmt = $conn->prepare("UPDATE educational_details SET verification_status = 'verified' WHERE user_id = ?");
        $stmt->execute([$userId]);

        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
