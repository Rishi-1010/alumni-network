<?php
session_start();
require_once '../../config/db_connection.php';

// If OTP is not set in session, redirect to home
if (!isset($_SESSION['otp']) || !isset($_SESSION['temp_email'])) {
    header("Location: ../../index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="../../assets/css/register.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <a href="../../index.html" class="home-link">
                <img src="../../assets/img/logo.png" alt="Alumni Network Logo">
                <span>Alumni Network</span>
            </a>
        </div>
        <div class="nav-links">
            <a href="../../index.html" class="home-btn">Home</a>
            <a href="../Login/login.php" class="login-btn">Login</a>
        </div>
    </nav>

    <div class="register-container">
        <form id="otpForm" class="registration-form" method="POST">
            <h2>OTP Verification</h2>
            <p class="verification-text">Please enter the OTP sent to your email: <?php echo htmlspecialchars($_SESSION['temp_email']); ?></p>
            
            <?php
            if(isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            ?>

            <div class="form-group">
                <label for="otp">Enter OTP*</label>
                <input type="text" id="otp" name="otp" required maxlength="6" pattern="[0-9]{6}">
            </div>

            <div class="form-group">
                <button type="submit" class="submit-btn">Verify OTP</button>
            </div>

            <div class="form-links">
                <a href="#" id="resendOtp">Resend OTP</a>
                <span>Wrong email? <a href="../../index.html">Go Back</a></span>
            </div>
        </form>
    </div>

    <script>
    document.getElementById('otpForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const otp = document.getElementById('otp').value;
        
        fetch('../../send_otp/verify_otp.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'otp=' + encodeURIComponent(otp)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show success message
                const successDiv = document.createElement('div');
                successDiv.className = 'alert alert-success';
                successDiv.textContent = 'OTP verified successfully! Redirecting to registration...';
                
                // Insert the message before the form
                document.querySelector('.registration-form').insertBefore(
                    successDiv, 
                    document.querySelector('.form-group')
                );

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = 'register.php';
                }, 2000);
            } else {
                // Show error in the existing error div or create a new one
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger';
                errorDiv.textContent = data.message;
                
                // Remove any existing error messages
                const existingError = document.querySelector('.alert-danger');
                if (existingError) {
                    existingError.remove();
                }
                
                // Insert the new error message
                document.querySelector('.registration-form').insertBefore(
                    errorDiv, 
                    document.querySelector('.form-group')
                );
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while verifying OTP');
        });
    });

    document.getElementById('resendOtp').addEventListener('click', function(e) {
        e.preventDefault();
        fetch('../../send_otp/resend_otp.php')
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while resending OTP');
        });
    });
    </script>
</body>
</html> 