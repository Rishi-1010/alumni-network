<?php
session_start();
require_once '../../config/db_connection.php';

// Security checks
if (!isset($_SESSION['admin_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit();
}

if (!isset($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit();
}

$db = new Database();
$conn = $db->connect();

$event_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header('HTTP/1.1 404 Not Found');
    exit();
}

header('Content-Type: application/json');
echo json_encode($event); 