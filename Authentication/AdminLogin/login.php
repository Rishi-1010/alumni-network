<?php
session_start();
require_once '../../config/db_connection.php';

if(isset($_SESSION['admin_id'])) {
    header("Location: ../../admin/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>\
<!-- <script src="../../assets/js/main.js"></script> -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-box">
            <h2>Admin Login</h2>
            <?php
            if(isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            ?>
            <form action="process_login.php" method="POST">
                <div class="form-group">
                    <input type="text" id="username" name="username" required>
                    <label for="username">Username</label>
                </div>
                
                <div class="form-group">
                    <input type="password" id="password" name="password" required>
                    <label for="password">Password</label>
                </div>

                <button type="submit" class="admin-login-btn">Login</button>
            </form>
            <div class="back-to-home">
                <a href="../../index.html">Back to Home</a>
            </div>
        </div>
    </div>
    <script src="../../assets/js/admin-login.js"></script>
</body>
</html> 