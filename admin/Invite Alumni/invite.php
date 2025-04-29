<?php
session_start();
require_once '../../config/db_connection.php';

// Make sure this is the very first check after session_start()
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../Authentication/AdminLogin/login.php");
    exit();
}

// Additional security: ensure the header redirect happens
if (headers_sent()) {
    die("Redirect failed. Please <a href='../../Authentication/AdminLogin/login.php'>click here</a>");
}

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_invitation'])) {
    $alumniEmail = $_POST['alumni_emails'];
    
    try {
        // Include PHPMailer function
        require_once '../../send_otp/PHPMailerFunction.php';
        
        $emails = explode(',', $alumniEmail);
        $emails = array_map('trim', $emails); // Remove whitespace

        // Send invitation
        $mailResult = sendInvitationEmail($emails);

        if ($mailResult === true) {
            $_SESSION['success'] = "Invitation sent successfully.";
        } elseif (is_string($mailResult)) {
            $_SESSION['error'] = $mailResult;
        } else {
            $_SESSION['error'] = "Failed to send invitation.";
        }
    } catch(Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite Alumni - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/navigation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .invite-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .btn-invite {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-invite:hover {
            background-color: #45a049;
        }
        .notification {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="dashboard-nav">
        <div class="logo">
            <img src="../../assets/img/logo.png" alt="Alumni Network Logo">
            <span>SRIMCA_BVPICS Alumni Network</span>
        </div>
        <div class="dashboard-navbar">
            <a href="../dashboard.php">Dashboard</a>
            <a href="../profile.php">Profile</a>
            <!-- <a href="../Alumni Management/totalalumnis.php">Total Alumni</a> -->
            <a href="../Authentication/AdminLogin/logout.php">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="invite-container">
        <h2><i class="fas fa-user-plus"></i> Invite Alumni</h2>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="notification success">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="notification error">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="alumni_emails">Alumni Email Addresses</label>
                <textarea 
                    class="form-control" 
                    id="alumni_emails" 
                    name="alumni_emails" 
                    rows="4" 
                    placeholder="Enter alumni email addresses (comma-separated)"
                    required
                ></textarea>
                <small class="form-text text-muted">Enter multiple email addresses separated by commas</small>
            </div>
            <button type="submit" name="send_invitation" class="btn btn-invite">
                <i class="fas fa-paper-plane"></i> Send Invitations
            </button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 