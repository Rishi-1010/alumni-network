<?php
session_start();
require_once '../../config/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Authentication/Login/login.php");
    exit();
}

// Check if event_id is provided
if (!isset($_GET['event_id'])) {
    $_SESSION['error'] = "No event specified.";
    header("Location: events.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

try {
    // First check if the event is upcoming
    $stmt = $conn->prepare("SELECT status FROM events WHERE event_id = ?");
    $stmt->execute([$_GET['event_id']]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        throw new Exception("Event not found.");
    }

    if ($event['status'] !== 'upcoming') {
        throw new Exception("Cannot cancel registration for non-upcoming events.");
    }

    // Delete the registration
    $stmt = $conn->prepare("DELETE FROM event_registrations WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$_GET['event_id'], $_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "Event registration cancelled successfully.";
    } else {
        throw new Exception("No registration found for this event.");
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header("Location: events.php");
exit(); 