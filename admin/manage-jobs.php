<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit;
}

// Handle job deletion
if (isset($_GET['delete'])) {
    $job_id = intval($_GET['delete']);
    $conn->query("DELETE FROM jobs WHERE id = $job_id");
    header("Location: manage-jobs.php");
    exit;
}

// Fetch jobs with provider info
$jobs = $conn->query("
    SELECT jobs.*, users.name AS provider_name 
    FROM jobs 
    JOIN users ON jobs.provider_id = users.id 
    ORDER BY jobs.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Jobs</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <div class="admin-header">
        <h1>Manage Job Listings</h1>
        <a href="admin-dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Company / Title</th>
                <th>Posted By</th>
                <th>Location</th>
                <th>Salary</th>
                <th>Job Type</th>
                <th>Posted On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($job = $jobs->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $job['id']; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($job['company_name']); ?></strong><br>
                    <?php echo htmlspecialchars($job['title']); ?>
                </td>
                <td><?php echo htmlspecialchars($job['provider_name']); ?></td>
                <td><?php echo htmlspecialchars($job['location']); ?></td>
                <td>$<?php echo number_format($job['salary'], 2); ?></td>
                <td><?php echo $job['job_type']; ?></td>
                <td><?php echo date("Y-m-d", strtotime($job['created_at'])); ?></td>
                <td>
                    <a href="?delete=<?php echo $job['id']; ?>" class="btn delete" onclick="return confirm('Delete this job?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
