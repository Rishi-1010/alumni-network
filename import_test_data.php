<?php
require_once 'config/db_connection.php';

// Check if admin is logged in
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: Authentication/AdminLogin/login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Read the SQL file
$sql = file_get_contents('test_data.sql');

try {
    // Execute the SQL statements
    $conn->exec($sql);
    echo "<div style='color: green; font-weight: bold; padding: 20px;'>Test data imported successfully!</div>";
    echo "<p>You can now test the search functionality in the admin dashboard.</p>";
    echo "<p><a href='admin/Alumni Management/totalalumnis.php'>Go to Total Alumni page</a></p>";
} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold; padding: 20px;'>Error importing test data: " . $e->getMessage() . "</div>";
}
?> 