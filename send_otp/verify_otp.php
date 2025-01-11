<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $enteredOtp = $_POST['otp'];

    // Check if OTP exists in session
    if (!isset($_SESSION['otp'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'OTP session expired. Please request a new OTP.'
        ]);
        exit();
    }

    // Check if the OTP is correct
    if ($enteredOtp == $_SESSION['otp']) {
        // Set verification flag and clean up
        $_SESSION['otp_verified'] = true;
        unset($_SESSION['otp']); // Remove OTP after successful verification
        
        echo json_encode([
            'status' => 'success',
            'message' => 'OTP verified successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid OTP. Please try again.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>
