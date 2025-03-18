<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'status' => 'error',
    'message' => 'OTP verification functionality has been removed.'
]);
?>
