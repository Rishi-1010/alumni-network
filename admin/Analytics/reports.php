<?php
session_start();
require_once '../../config/db_connection.php';

// Security checks
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../Authentication/AdminLogin/login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Fetch various statistics
try {
    // Total alumni count
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users");
    $totalAlumni = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Verified vs Unverified
    $stmt = $conn->query("SELECT 
                            COALESCE(verification_status, 'pending') as status,
                            COUNT(*) as count 
                         FROM educational_details 
                         GROUP BY verification_status");
    $verificationStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Batch-wise distribution (graduation year)
    $stmt = $conn->query("SELECT graduation_year, COUNT(*) as count 
                         FROM educational_details 
                         GROUP BY graduation_year 
                         ORDER BY graduation_year DESC");
    $batchStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Professional Status Distribution
    $stmt = $conn->query("SELECT 
                            current_status,
                            COUNT(*) as count 
                         FROM professional_status 
                         GROUP BY current_status");
    $professionalStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Skills Distribution
    $stmt = $conn->query("SELECT 
                            proficiency_level,
                            COUNT(*) as count 
                         FROM skills 
                         GROUP BY proficiency_level");
    $skillStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Career Goals Status
    $stmt = $conn->query("SELECT 
                            status,
                            COUNT(*) as count 
                         FROM career_goals 
                         GROUP BY status");
    $careerGoalStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/invite.css">
    <link rel="stylesheet" href="../../assets/css/navigation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .reports-container {
            padding: 2rem;
        }
        .report-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin: 1rem 0;
        }
        .stat-highlight {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 1rem 0;
        }
        .stat-highlight h3 {
            color: #4a90e2;
            font-size: 2rem;
            margin: 0;
        }
        .stat-highlight p {
            color: #6c757d;
            margin: 0;
        }
        .download-btn {
            background: #4a90e2;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
        }
        .download-btn:hover {
            background: #357abd;
            color: white;
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
            <a href="../Authentication/AdminLogin/logout.php">Logout</a>
        </div>
    </nav>

    <div class="reports-container">
        <div class="welcome-section">
            <h1>Reports & Analytics</h1>
            <p>Comprehensive insights about your alumni network</p>
        </div>

        <!-- Key Metrics -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="stat-highlight">
                    <h3><?php echo $totalAlumni; ?></h3>
                    <p>Total Alumni</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-highlight">
                    <h3><?php 
                        $employed = 0;
                        foreach($professionalStats as $stat) {
                            if($stat['current_status'] == 'employed') {
                                $employed = $stat['count'];
                                break;
                            }
                        }
                        echo $employed;
                    ?></h3>
                    <p>Employed Alumni</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-highlight">
                    <h3><?php 
                        $verified = 0;
                        foreach($verificationStats as $stat) {
                            if($stat['status'] == 'verified') {
                                $verified = $stat['count'];
                                break;
                            }
                        }
                        echo $verified;
                    ?></h3>
                    <p>Verified Alumni</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-highlight">
                    <h3><?php 
                        $achieved = 0;
                        foreach($careerGoalStats as $stat) {
                            if($stat['status'] == 'achieved') {
                                $achieved = $stat['count'];
                                break;
                            }
                        }
                        echo $achieved;
                    ?></h3>
                    <p>Achieved Career Goals</p>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="row mt-4">
            <div class="col-md-6 mb-4">
                <div class="report-card h-100">
                    <h2>Verification Status</h2>
                    <div class="chart-container">
                        <canvas id="verificationChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="report-card h-100">
                    <h2>Professional Status Distribution</h2>
                    <div class="chart-container">
                        <canvas id="professionalChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="report-card h-100">
                    <h2>Batch-wise Distribution</h2>
                    <div class="chart-container">
                        <canvas id="batchChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="report-card h-100">
                    <h2>Skills Proficiency Distribution</h2>
                    <div class="chart-container">
                        <canvas id="skillsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="report-card h-100">
                    <h2>Career Goals Status</h2>
                    <div class="chart-container">
                        <canvas id="careerGoalsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Verification Status Chart
        new Chart(document.getElementById('verificationChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($verificationStats, 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($verificationStats, 'count')); ?>,
                    backgroundColor: ['#4a90e2', '#e74c3c', '#f1c40f']
                }]
            }
        });

        // Professional Status Chart
        new Chart(document.getElementById('professionalChart'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($professionalStats, 'current_status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($professionalStats, 'count')); ?>,
                    backgroundColor: ['#2ecc71', '#e74c3c', '#f1c40f', '#9b59b6']
                }]
            }
        });

        // Batch-wise Distribution Chart
        new Chart(document.getElementById('batchChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($batchStats, 'graduation_year')); ?>,
                datasets: [{
                    label: 'Number of Alumni',
                    data: <?php echo json_encode(array_column($batchStats, 'count')); ?>,
                    borderColor: '#4a90e2',
                    tension: 0.1
                }]
            }
        });

        // Skills Distribution Chart
        new Chart(document.getElementById('skillsChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($skillStats, 'proficiency_level')); ?>,
                datasets: [{
                    label: 'Number of Skills',
                    data: <?php echo json_encode(array_column($skillStats, 'count')); ?>,
                    backgroundColor: '#4a90e2'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Career Goals Chart
        new Chart(document.getElementById('careerGoalsChart'), {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($careerGoalStats, 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($careerGoalStats, 'count')); ?>,
                    backgroundColor: ['#2ecc71', '#f1c40f', '#4a90e2', '#e74c3c']
                }]
            }
        });
    </script>
</body>
</html> 