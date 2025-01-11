<?php
// path/to/PHPMailerFunction.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Include PHPMailer using Composer (or manual inclusion)

function sendInvitationEmail($toEmail, $otp) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'rishi.bardoliya@gmail.com'; // Replace with your email
        $mail->Password = 'wahttezqfhguywph'; // Replace with your email password or App-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('rishi.bardoliya@gmail.com', 'Admin');
        $mail->addAddress($toEmail);  // Add alumni email

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Alumni Network Registration Invitation';
        $mail->Body    = "
            <h3>Welcome to the Alumni Network</h3>
            <p>You have been invited to join the Alumni Network by an administrator.</p>
            <p>Your OTP for registration is: <strong>$otp</strong></p>
            <p>Please click the link below to verify your OTP and complete the registration:</p>
            <a href='http://localhost/alumni-network/Authentication/Registration/otpverification.php'>Click here to verify OTP</a>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
