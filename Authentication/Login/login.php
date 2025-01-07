<?php
session_start();
require_once '../../config/db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Login</title>
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
            <a href="../Registration/register.php" class="register-btn">Register</a>
        </div>
    </nav>

    <div class="register-container">
        <form id="loginForm" class="registration-form" action="process_login.php" method="POST">
            <h2>Alumni Login</h2><br>
            
            <?php
            if(isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            if(isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            ?>

            <div class="form-group">
                <label for="email">Email Address*</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password*</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <button type="submit" class="submit-btn">Login</button>
            </div>

            <div class="form-links">
                <a href="forgot_password.php">Forgot Password?</a>
                <span>Don't have an account? <a href="../Registration/register.php">Register</a></span>
            </div>
        </form>
    </div>

    <script src="../../assets/js/login.js"></script>
</body>
</html>
