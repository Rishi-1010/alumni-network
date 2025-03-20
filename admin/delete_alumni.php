<?php
session_start();
require_once '../config/db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../Authentication/AdminLogin/login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: totalalumnis.php");
    exit();
}

$alumni_id = $_GET['id'];

$db = new Database();
$conn = $db->connect();

try {
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$alumni_id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "Alumni member removed successfully.";
    } else {
        $_SESSION['error'] = "Failed to remove alumni member.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header("Location: totalalumnis.php");
exit();
?>
