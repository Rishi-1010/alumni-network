<?php
// path/to/PHPMailerFunction.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; // Updated path to autoload.php

function sendInvitationEmail(array $toEmails) {
    $success = true;
    $errorMessages = [];

    foreach ($toEmails as $toEmail) {
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
            $mail->Subject = 'Welcome to the SRIMCA_BVPICS Alumni Network!';
            $mail->Body    = "
                <h2 style='color: #333;'>Welcome to the SRIMCA_BVPICS Alumni Network!</h2>
                <p>Dear Alumni,</p>
                <p>You are invited to join the <b>SRIMCA_BVPICS Alumni Network</b>! This is a platform to connect with fellow alumni, share your experiences, and stay updated on the latest news and events.</p>
                <p>Please click the link below to complete your registration:</p>
                <a href='https://electro-municipality-thoughts-waters.trycloudflare.com/alumni-network/authentication/Registration/register.php' style='background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Register Now</a>
                <p>Best regards,</p>
                <p>The SRIMCA_BVPICS Alumni Team</p>
            ";
            $mail->AltBody = "
                Dear Alumni,
                You are invited to join the SRIMCA_BVPICS Alumni Network! This is a platform to connect with fellow alumni, share your experiences, and stay updated on the latest news and events.
                Please click the link below to complete your registration:
                https://electro-municipality-thoughts-waters.trycloudflare.com/alumni-network/authentication/Registration/register.php
                Best regards,
                The SRIMCA_BVPICS Alumni Team
            ";

            $mail->send();
        } catch (Exception $e) {
            $success = false;
            $errorMessages[] = "Failed to send to " . htmlspecialchars($toEmail) . ": " . $mail->ErrorInfo;
        }
    }

    if (!empty($errorMessages)) {
        return implode("<br>", $errorMessages);
    }

    return $success;
}
?>
