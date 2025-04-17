<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit;
}

// Count stats
$total_users = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];
$total_jobs = $conn->query("SELECT COUNT(*) AS count FROM jobs")->fetch_assoc()['count'];
$total_applications = $conn->query("SELECT COUNT(*) AS count FROM applications")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin-style.css?v=<?= time(); ?>">
    <style>
        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 100px;
            justify-content: center;
        }

        .card {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 25px 30px;
            width: 250px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .card h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .card p {
            font-size: 28px;
            color: #007bff;
            margin: 0;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <div class="admin-dashboard-container">

        <!-- Content overlay -->
        <div class="dashboard-content">
            <div class="admin-header">
                <h1>Welcome, <?php echo $_SESSION['admin_username']; ?></h1>
                <a href="admin-logout.php" class="logout-btn">Logout</a>
            </div>

            <div class="dashboard-cards">
                <div class="card" style="padding-left:20px">
                    <h3>Total Users</h3>
                    <p><?php echo $total_users; ?></p>
                </div>
                <div class="card" style="padding-left:20px">
                    <h3>Total Jobs</h3>
                    <p><?php echo $total_jobs; ?></p>
                </div>
                <div class="card" style="padding-left:20px">
                    <h3>Total Applications</h3>
                    <p><?php echo $total_applications; ?></p>
                </div>
            </div>

            <div class="admin-links">
                <a href="manage-users.php">Manage Users</a>
                <a href="manage-jobs.php">Manage Jobs</a>
                <!--<a href="verify-job.php">Verify Job Posts</a>-->
                <a href="admin-profile.php">Admin Profile</a>
            </div>
        </div>
    </div>
</body>

</html>
