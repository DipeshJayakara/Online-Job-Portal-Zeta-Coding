<?php
// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require database and authentication
require_once '../config/connection.php';
require_once 'admin-auth.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit();
}

// Fetch total users and total jobs
$summaryQuery = "
    SELECT 
        (SELECT COUNT(*) FROM users) AS total_users, 
        (SELECT COUNT(*) FROM jobs) AS total_jobs
";
$summaryResult = mysqli_query($conn, $summaryQuery);
$summaryData = mysqli_fetch_assoc($summaryResult);
$totalUsers = $summaryData['total_users'];
$totalJobs = $summaryData['total_jobs'];

// Fetch latest 10 job listings with applicant count
$jobsQuery = "
    SELECT j.id, j.title, j.company_name, COUNT(a.id) AS total_applicants 
    FROM jobs j
    LEFT JOIN applications a ON j.id = a.job_id 
    GROUP BY j.id 
    ORDER BY j.created_at DESC 
    LIMIT 10
";
$jobsResult = mysqli_query($conn, $jobsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <h2>Welcome to Admin Dashboard</h2>

        <div class="stats-container">
            <div class="card">Total Users: <strong><?php echo $totalUsers; ?></strong></div>
            <div class="card">Total Job Listings: <strong><?php echo $totalJobs; ?></strong></div>
        </div>

        <h3>Latest Job Listings</h3>
        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Applicants</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($job = mysqli_fetch_assoc($jobsResult)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                        <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                        <td><?php echo $job['total_applicants']; ?></td>
                        <td><a href="verify-job.php?job_id=<?php echo $job['id']; ?>">Verify Job</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h3>Admin Actions</h3>
        <div class="admin-actions">
            <a href="manage-users.php" class="btn">Manage Users</a>
            <a href="manage-jobs.php" class="btn">Manage Jobs</a>
            <a href="change-password.php" class="btn">Change Password</a>
            <a href="admin-logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</body>
</html>
