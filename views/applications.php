<?php
require_once '../config/connection.php';
require_once '../includes/auth.php'; // Starts session

// Check if provider is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch job applications for the logged-in provider
$stmt = $conn->prepare("
    SELECT 
        applications.id,
        applications.applicant_id,
        users.name AS applicant_name,
        users.resume,
        applications.ats_score,
        jobs.title AS job_title,
        applications.status
    FROM applications
    JOIN users ON users.id = applications.applicant_id
    JOIN jobs ON jobs.id = applications.job_id
    WHERE jobs.provider_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applications</title>
    <link rel="stylesheet" href="../assets/css/application.css">
</head>
<body>
    <h2>Applications for Your Posted Jobs</h2>

    <!-- Status update success message -->
    <?php if (isset($_GET['updated']) && $_GET['updated'] === 'true'): ?>
        <p style="color: green;">Application statuses updated successfully!</p>
    <?php endif; ?>

    <form method="POST" action="update-status.php">
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Applicant ID</th>
                    <th>Applicant Name</th>
                    <th>Job Title</th>
                    <th>Resume</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['applicant_id']) ?></td>
                        <td><?= htmlspecialchars($row['applicant_name']) ?></td>
                        <td><?= htmlspecialchars($row['job_title']) ?></td>
                        <td>
                            <?php if ($row['resume']) { ?>
                                <a href="../uploads/resumes/<?= htmlspecialchars($row['resume']) ?>" target="_blank">View</a>
                            <?php } else { ?>
                                No Resume
                            <?php } ?>
                        </td>
                        <td>
                            <input type="hidden" name="application_ids[]" value="<?= $row['id'] ?>">
                            <select name="statuses[]">
                                <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Accepted" <?= $row['status'] == 'Accepted' ? 'selected' : '' ?>>Accepted</option>
                                <option value="Rejected" <?= $row['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <br>
        <div style="display: flex; justify-content: center; align-items: center;">
            <button type="submit">Update Status</button>
        </div>
    </form>

    <br>
    <div class="center-link" style="display: flex; justify-content: center; align-items: center;">
        <a href="dashboard.php" class="animated-link">Back to Dashboard</a>
    </div>

</body>
</html>
