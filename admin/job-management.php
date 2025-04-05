<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$query = "SELECT j.id, j.title, j.company, j.location, j.date_posted, j.status, COUNT(a.id) AS applicants
          FROM jobs j
          LEFT JOIN applications a ON j.id = a.job_id
          GROUP BY j.id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Management</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <script>
    function deleteJob(id) {
        if (confirm("Are you sure you want to delete this job?")) {
            fetch('job-actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete&job_id=${id}`
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
        }
    }

    function toggleStatus(id, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        fetch('job-actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=toggle&job_id=${id}&status=${newStatus}`
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            location.reload();
        });
    }
    </script>
</head>
<body>
    <div class="dashboard-container">
        <h2>Job Management</h2>
        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Location</th>
                    <th>Date Posted</th>
                    <th>Total Applicants</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($job = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= htmlspecialchars($job['title']) ?></td>
                    <td><?= htmlspecialchars($job['company']) ?></td>
                    <td><?= htmlspecialchars($job['location']) ?></td>
                    <td><?= date('Y-m-d', strtotime($job['date_posted'])) ?></td>
                    <td><?= $job['applicants'] ?></td>
                    <td><?= $job['status'] === 'active' ? 'Active' : 'Inactive' ?></td>
                    <td>
                        <a href="edit-job.php?id=<?= $job['id'] ?>">Edit</a> |
                        <a href="#" onclick="deleteJob(<?= $job['id'] ?>)">Delete</a> |
                        <a href="#" onclick="toggleStatus(<?= $job['id'] ?>, '<?= $job['status'] ?>')">
                            <?= $job['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <br><a href="admin-dashboard.php">Back to Admin Dashboard</a>
    </div>
</body>
</html>
