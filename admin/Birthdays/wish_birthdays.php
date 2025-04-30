<?php
session_start();
require_once '../../config/db_connection.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../Authentication/AdminLogin/login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Get current month and day
$currentMonth = date('m');
$currentDay = date('d');

// Query to get alumni with birthdays today
$query = "SELECT u.user_id, u.fullname, u.email, u.phone, u.dob
          FROM users u 
          WHERE MONTH(u.dob) = ? 
          AND DAY(u.dob) = ?";

$stmt = $conn->prepare($query);
$stmt->execute([$currentMonth, $currentDay]);
$birthdayAlumni = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query to get all upcoming birthdays in current month
$upcomingQuery = "SELECT u.user_id, u.fullname, u.email, u.phone, u.dob
                 FROM users u 
                 WHERE MONTH(u.dob) = ? 
                 AND DAY(u.dob) > ?
                 ORDER BY DAY(u.dob) ASC";

$stmt = $conn->prepare($upcomingQuery);
$stmt->execute([$currentMonth, $currentDay]);
$upcomingBirthdays = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Birthday Wishes - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/navigation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .birthday-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .birthday-icon {
            font-size: 2em;
            color: #ff6b6b;
            margin-bottom: 10px;
        }
        .wish-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .upcoming-birthday {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            transition: transform 0.2s;
        }
        .upcoming-birthday:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .birthday-date {
            font-size: 0.9em;
            color: #6c757d;
        }
        .section-title {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
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
        <div class="dashboard-navbar" id="navLinks">
            <a href="../dashboard.php">Dashboard</a>
            <a href="../profile.php">Profile</a>
            <a href="../../Authentication/AdminLogin/logout.php">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Today's Birthdays Section -->
                <h2 class="section-title">
                    <i class="fas fa-birthday-cake"></i> 
                    Today's Birthdays
                </h2>

                <?php if (empty($birthdayAlumni)): ?>
                    <div class="alert alert-info">
                        No alumni have birthdays today.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($birthdayAlumni as $alumni): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="birthday-card">
                                    <div class="text-center">
                                        <i class="fas fa-birthday-cake birthday-icon"></i>
                                        <h4><?php echo htmlspecialchars($alumni['fullname']); ?></h4>
                                        <p class="text-muted">
                                            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($alumni['email']); ?><br>
                                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($alumni['phone']); ?>
                                        </p>
                                    </div>
                                    <div class="wish-form">
                                        <form action="send_wish.php" method="POST">
                                            <input type="hidden" name="user_id" value="<?php echo $alumni['user_id']; ?>">
                                            <div class="mb-3">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="message_type" value="default" id="defaultMessage<?php echo $alumni['user_id']; ?>" checked>
                                                    <label class="form-check-label" for="defaultMessage<?php echo $alumni['user_id']; ?>">
                                                        Use Default Birthday Message
                                                    </label>
                                                </div>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="message_type" value="custom" id="customMessage<?php echo $alumni['user_id']; ?>">
                                                    <label class="form-check-label" for="customMessage<?php echo $alumni['user_id']; ?>">
                                                        Customize Birthday Message
                                                    </label>
                                                </div>
                                                <div class="custom-message-box" style="display: none;">
                                                    <label for="wish_message" class="form-label">Custom Birthday Message</label>
                                                    <textarea class="form-control" id="wish_message<?php echo $alumni['user_id']; ?>" name="wish_message" rows="3" placeholder="Write your custom birthday message here..."></textarea>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-paper-plane"></i> Send Birthday Wish
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Upcoming Birthdays Section -->
                <h2 class="section-title mt-5">
                    <i class="fas fa-calendar-alt"></i> 
                    Upcoming Birthdays This Month
                </h2>

                <?php if (empty($upcomingBirthdays)): ?>
                    <div class="alert alert-info">
                        No more birthdays this month.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($upcomingBirthdays as $alumni): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="upcoming-birthday">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1"><?php echo htmlspecialchars($alumni['fullname']); ?></h5>
                                            <p class="birthday-date mb-0">
                                                <i class="fas fa-calendar-day"></i> 
                                                <?php echo date('F j', strtotime($alumni['dob'])); ?>
                                            </p>
                                        </div>
                                        <a href="mailto:<?php echo htmlspecialchars($alumni['email']); ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-envelope"></i> Send Email
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle message type toggle
        document.querySelectorAll('input[name="message_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const userId = this.id.replace(/[^0-9]/g, '');
                const customMessageBox = this.closest('.wish-form').querySelector('.custom-message-box');
                const textarea = document.getElementById('wish_message' + userId);
                
                if (this.value === 'custom') {
                    customMessageBox.style.display = 'block';
                    textarea.required = true;
                } else {
                    customMessageBox.style.display = 'none';
                    textarea.required = false;
                }
            });
        });
    </script>
</body>
</html> 