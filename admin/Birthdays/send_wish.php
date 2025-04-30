<?php
session_start();
require_once '../../config/db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../Authentication/AdminLogin/login.php");
    exit();
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: wish_birthdays.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Get form data
$user_id = $_POST['user_id'] ?? '';
$message_type = $_POST['message_type'] ?? 'default';
$wish_message = $_POST['wish_message'] ?? '';

// Validate input
if (empty($user_id) || ($message_type === 'custom' && empty($wish_message))) {
    $_SESSION['error'] = "Please fill in all required fields.";
    header("Location: wish_birthdays.php");
    exit();
}

try {
    // Get user details
    $stmt = $conn->prepare("SELECT email, fullname FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found.");
    }

    // Get message content
    $customMessage = isset($_POST['wish_message']) && !empty($_POST['wish_message']) 
        ? $_POST['wish_message'] 
        : null;

    // Default birthday message template
    function getDefaultBirthdayMessage($name) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #333; text-align: center;'>Happy Birthday, {$name}! 🎉</h2>
            <p style='color: #555; font-size: 16px; line-height: 1.6;'>
                Wishing you a fantastic birthday filled with joy, laughter, and wonderful moments! 
                May this special day bring you everything you wish for and more.
            </p>
            <p style='color: #555; font-size: 16px; line-height: 1.6;'>
                As a valued member of our alumni network, we're grateful to have you as part of our community. 
                Here's to another year of success, growth, and amazing achievements!
            </p>
            <div style='text-align: center; margin-top: 30px;'>
                <p style='color: #777; font-style: italic;'>Best wishes,<br>SRIMCA_BVPICS Alumni Network Team</p>
            </div>
        </div>";
    }

    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'rishi.bardoliya@gmail.com';
    $mail->Password = 'wahttezqfhguywph';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('rishi.bardoliya@gmail.com', 'SRIMCA_BVPICS Alumni Network');
    $mail->addAddress($user['email'], $user['fullname']);

    // Content
    $mail->isHTML(true);
    $mail->Subject = '🎂 Happy Birthday from SRIMCA_BVPICS Alumni Network!';
    
    // Use custom message if provided, otherwise use default template
    $mail->Body = $customMessage ?? getDefaultBirthdayMessage($user['fullname']);
    
    // Plain text version
    $mail->AltBody = strip_tags($mail->Body);

    $mail->send();
    $_SESSION['success'] = "Birthday wish sent successfully to " . htmlspecialchars($user['fullname']);
} catch (Exception $e) {
    $_SESSION['error'] = "Failed to send birthday wish: " . $mail->ErrorInfo;
}

header("Location: wish_birthdays.php");
exit(); 