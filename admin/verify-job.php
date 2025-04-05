<?php
require_once 'admin-auth.php';
require_once '../config/connection.php';

// Handle job verification
if (isset($_POST['verify_job']) && isset($_POST['job_id'])) {
    $job_id = intval($_POST['job_id']);
    $stmt = $conn->prepare("UPDATE jobs SET status = 'verified' WHERE id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    echo "<script>alert('Job verified successfully!'); window.location.href='verify-job.php';</script>";
    exit();
}

// Handle job rejection
if (isset($_POST['reject_job']) && isset($_POST['job_id'])) {
    $job_id = intval($_POST['job_id']);
    $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    echo "<script>alert('Job rejected successfully!'); window.location.href='verify-job.php';</script>";
    exit();
}

// Fetch all pending jobs
$stmt = $conn->prepare("
    SELECT j.id, j.title, j.company_name, j.description, j.status, j.created_at, u.full_name AS posted_by
    FROM jobs j
    JOIN users u ON j.posted_by = u.id
    WHERE j.status = 'pending'
    ORDER BY j.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Jobs</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <style>
        td, th { padding: 8px; vertical-align: top; }
        form { display: inline-block; margin-right: 6px; }
        .actions button {
            padding: 4px 10px;
            border: none;
            cursor: pointer;
        }
        .verify-btn { background-color: #4CAF50; color: white; }
        .reject-btn { background-color: #f44336; color: white; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Pending Job Listings for Verification</h2>
        <table border="1" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Company</th>
                    <th>Posted By</th>
                    <th>Description</th>
                    <th>Posted On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($job = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $job['id']; ?></td>
                            <td><?php echo htmlspecialchars($job['title']); ?></td>
                            <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($job['posted_by']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($job['description'])); ?></td>
                            <td><?php echo $job['created_at']; ?></td>
                            <td class="actions">
                                <form method="POST">
                                    <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                    <button type="submit" name="verify_job" class="verify-btn">✅ Verify</button>
                                </form>
                                <form method="POST" onsubmit="return confirm('Reject this job?');">
                                    <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                    <button type="submit" name="reject_job" class="reject-btn">❌ Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                <?php else: ?>
                    <tr><td colspan="7">No pending jobs to verify.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <a href="admin-dashboard.php">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
