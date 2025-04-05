<?php
session_start();
require_once __DIR__ . '/../config/connection.php'; // Database connection

// ✅ Redirect if user is not authenticated
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css"> <!-- Ensure path is correct -->
</head>
<body>

    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <h2>Welcome to Your Dashboard</h2>

        <!-- ✅ Dashboard for Job Seekers -->
        <?php if ($user_role === 'seeker') { ?>
            <h3>Available Actions:</h3>
            <a href="job-listings.php">Browse Jobs</a>
            
            <h3>My Applications</h3>
            <?php
            $stmt = $conn->prepare("
                SELECT jobs.id, jobs.title 
                FROM applications 
                JOIN jobs ON applications.job_id = jobs.id 
                WHERE applications.applicant_id = ?
            ");
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    echo "<ul>";
                    while ($job = $result->fetch_assoc()) {
                        echo "<li><a href='job-details.php?job_id=" . $job['id'] . "'>" . htmlspecialchars($job['title']) . "</a></li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No applications yet.</p>";
                }
            } else {
                echo "<p>Error fetching applications: " . $stmt->error . "</p>";
            }
            $stmt->close();
            ?>
        <?php } ?>

        <!-- ✅ Dashboard for Job Providers -->
        <?php if ($user_role === 'provider') { ?>
            <h3>Available Actions:</h3>
            <a href="post-job.php">Post a Job</a>
            <a href="applications.php">Manage Applications</a>

            <h3>Your Posted Jobs</h3>
            <?php
            $stmt = $conn->prepare("SELECT id, title FROM jobs WHERE provider_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                echo "<ul>";
                while ($job = $result->fetch_assoc()) {
                    echo "<li><a href='job-details.php?job_id=" . $job['id'] . "'>" . htmlspecialchars($job['title']) . "</a></li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No jobs posted yet.</p>";
            }
            $stmt->close();
            ?>
        <?php } ?>

        <!-- ✅ Dashboard for Admin -->
        <?php if ($user_role === 'admin') { ?>
            <h3>Admin Panel</h3>
            <a href="../admin/admin-dashboard.php">Admin Dashboard</a>
            <a href="../admin/manage-users.php">Manage Users</a>
            <a href="../admin/manage-jobs.php">Manage Jobs</a>
        <?php } ?>

        <h3>Profile & Logout</h3>
        <a href="profile.php">Edit Profile</a>
        <a href="../logout.php">Logout</a>
    </div>

    <?php include '../includes/footer.php'; ?>

</body>
</html>
