<?php
require_once '../config/connection.php';
require_once '../includes/auth.php'; // auth.php already starts session

// ✅ Ensure user is authenticated and is a Job Provider
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch applications with applicant ID
$applicationsQuery = $conn->prepare("
    SELECT 
        applications.id, 
        applications.applicant_id, 
        users.name AS applicant_name, 
        users.resume, 
        applications.ats_score, 
        jobs.title AS job_title, 
        applications.status
    FROM applications
    JOIN jobs ON applications.job_id = jobs.id
    JOIN users ON applications.applicant_id = users.id
    WHERE jobs.provider_id = ?
");
$applicationsQuery->bind_param("i", $user_id);
$applicationsQuery->execute();
$result = $applicationsQuery->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applications</title>
    <link rel="stylesheet" href="../assets/css/jobs.css">
</head>
<body>
    <h2>Job Applications</h2>
    <table border="1">
        <tr>
            <th>Applicant ID</th>
            <th>Applicant Name</th>
            <th>Job Title</th>
            <th>Resume</th>
            <th>ATS Score</th>
            <th>Status</th>
        </tr>
        <?php while ($application = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($application['applicant_id']); ?></td>
                <td><?php echo htmlspecialchars($application['applicant_name']); ?></td>
                <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                <td>
                    <?php if ($application['resume']) { ?>
                        <a href="../uploads/resumes/<?php echo htmlspecialchars($application['resume']); ?>" target="_blank">Download</a>
                    <?php } else { ?>
                        No Resume
                    <?php } ?>
                </td>
                <td>
                    <?php echo $application['ats_score'] ? htmlspecialchars($application['ats_score']) . "%" : "Not Available"; ?>
                </td>
                <td><?php echo htmlspecialchars($application['status']); ?></td>
            </tr>
        <?php } ?>
    </table>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
