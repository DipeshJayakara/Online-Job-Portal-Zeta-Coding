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
    <link rel="stylesheet" href="admin-style.css">
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
                <div class="card">
                    <h3>Total Users</h3>
                    <p><?php echo $total_users; ?></p>
                </div>
                <div class="card">
                    <h3>Total Jobs</h3>
                    <p><?php echo $total_jobs; ?></p>
                </div>
                <div class="card">
                    <h3>Total Applications</h3>
                    <p><?php echo $total_applications; ?></p>
                </div>
            </div>

            <div class="admin-links">
                <a href="manage-users.php">Manage Users</a>
                <a href="manage-jobs.php">Manage Jobs</a>
                <a href="verify-job.php">Verify Job Posts</a>
                <a href="admin-profile.php">Admin Profile</a>
            </div>
        </div>
    </div>
</body>

</html>
