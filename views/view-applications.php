<?php
require_once '../config/connection.php';
require_once '../includes/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch applications
$query = $conn->prepare("
    SELECT applications.id, applications.applicant_id, users.name AS applicant_name, users.resume, applications.ats_score,
           jobs.title AS job_title, applications.status
    FROM applications
    JOIN jobs ON applications.job_id = jobs.id
    JOIN users ON applications.applicant_id = users.id
    WHERE jobs.provider_id = ?
");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applications</title>
    <link rel="stylesheet" href="../assets/css/application.css">
</head>
<body>
    <h2>Job Applications</h2>
    <form method="POST" action="update_status.php">
        <table border="1">
            <tr>
                <th>Applicant ID</th>
                <th>Name</th>
                <th>Job Title</th>
                <th>Resume</th>
                <th>ATS Score</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['applicant_id']) ?></td>
                    <td><?= htmlspecialchars($row['applicant_name']) ?></td>
                    <td><?= htmlspecialchars($row['job_title']) ?></td>
                    <td>
                        <?php if ($row['resume']) { ?>
                            <a href="../uploads/resumes/<?= htmlspecialchars($row['resume']) ?>" target="_blank">Download</a>
                        <?php } else { echo "No Resume"; } ?>
                    </td>
                    <td><?= $row['ats_score'] ?? "N/A" ?></td>
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
        </table>
        <br>
        <button type="submit">Update Status</button>
    </form>
    <br><a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
