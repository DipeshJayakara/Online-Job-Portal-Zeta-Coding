<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit;
}

// Approve or Reject
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE jobs SET verified = 1 WHERE id = $id");
    header("Location: verify-job.php");
    exit;
}

if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $conn->query("DELETE FROM jobs WHERE id = $id");
    header("Location: verify-job.php");
    exit;
}

// Fetch unverified jobs
$unverified_jobs = $conn->query("
    SELECT jobs.*, users.name AS provider_name 
    FROM jobs 
    JOIN users ON jobs.provider_id = users.id 
    WHERE jobs.verified = 0 
    ORDER BY jobs.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify Jobs</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <div class="admin-header">
        <h1>Verify Job Posts</h1>
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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($job = $unverified_jobs->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $job['id']; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($job['company_name']); ?></strong><br>
                    <?php echo htmlspecialchars($job['title']); ?>
                </td>
                <td><?php echo htmlspecialchars($job['provider_name']); ?></td>
                <td><?php echo htmlspecialchars($job['location']); ?></td>
                <td>$<?php echo number_format($job['salary'], 2); ?></td>
                <td>
                    <a href="?approve=<?php echo $job['id']; ?>" class="btn approve">Approve</a>
                    <a href="?reject=<?php echo $job['id']; ?>" class="btn delete" onclick="return confirm('Reject this job?')">Reject</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
