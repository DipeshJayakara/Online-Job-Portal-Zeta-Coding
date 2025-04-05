<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$query = "SELECT j.id, j.title, j.company, j.description, COUNT(a.id) AS total_applicants 
          FROM jobs j 
          LEFT JOIN applications a ON j.id = a.job_id 
          GROUP BY j.id";
$jobsResult = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Jobs</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <script>
    function deleteJob(id) {
        if (confirm("Are you sure you want to delete this job?")) {
            fetch("job-actions.php", {
                method: "POST",
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=delete&job_id=${id}`
            })
            .then(res => res.text())
            .then(msg => {
                alert(msg);
                location.reload();
            });
        }
    }

    function showEditForm(id, title, company, description) {
        document.getElementById('job_id').value = id;
        document.getElementById('title').value = title;
        document.getElementById('company').value = company;
        document.getElementById('description').value = description;
        document.getElementById('edit-form').style.display = 'block';
        window.scrollTo(0, document.body.scrollHeight);
    }

    function updateJob(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new URLSearchParams(new FormData(form)).toString();

        fetch("job-actions.php", {
            method: "POST",
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: formData
        })
        .then(res => res.text())
        .then(msg => {
            alert(msg);
            location.reload();
        });
    }
    </script>
</head>
<body>
    <div class="dashboard-container">
        <h2>Manage Job Listings</h2>
        <a href="add-job.php" class="btn btn-primary">Add New Job</a>

        <table>
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Total Applicants</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($job = mysqli_fetch_assoc($jobsResult)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($job['title']) ?></td>
                        <td><?= htmlspecialchars($job['company']) ?></td>
                        <td><?= $job['total_applicants'] ?></td>
                        <td>
                            <a href="#" onclick="showEditForm(
                                <?= $job['id'] ?>,
                                `<?= htmlspecialchars($job['title'], ENT_QUOTES) ?>`,
                                `<?= htmlspecialchars($job['company'], ENT_QUOTES) ?>`,
                                `<?= htmlspecialchars($job['description'], ENT_QUOTES) ?>`
                            )">Edit</a> |
                            <a href="#" onclick="deleteJob(<?= $job['id'] ?>)">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Edit Form (hidden by default) -->
        <div id="edit-form" style="display:none; margin-top: 30px;">
            <h3>Edit Job</h3>
            <form onsubmit="updateJob(event)">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="job_id" id="job_id">

                <label>Job Title:</label>
                <input type="text" name="title" id="title" required>

                <label>Company:</label>
                <input type="text" name="company" id="company" required>

                <label>Description:</label>
                <textarea name="description" id="description" required></textarea>

                <button type="submit">Update Job</button>
            </form>
        </div>

        <br><a href="admin-dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
