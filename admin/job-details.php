<?php
session_start();
require_once '../config/connection.php';

// Redirect if admin is not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

// Check if job_id is provided in the URL
if (!isset($_GET['job_id']) || empty($_GET['job_id'])) {
    echo "<script>alert('Job ID is required'); window.location.href='manage-jobs.php';</script>";
    exit();
}

$job_id = $_GET['job_id'];

// Fetch job details
$query = "SELECT * FROM jobs WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$job_result = $stmt->get_result();
$job = $job_result->fetch_assoc();
$stmt->close();

if (!$job) {
    echo "<script>alert('Job not found'); window.location.href='manage-jobs.php';</script>";
    exit();
}

// Fetch applicants for this job
$apply_query = "SELECT a.id AS applicant_id, u.name, u.email, a.resume FROM applications a 
                LEFT JOIN users u ON a.user_id = u.id 
                WHERE a.job_id = ?";
$apply_stmt = $conn->prepare($apply_query);
$apply_stmt->bind_param("i", $job_id);
$apply_stmt->execute();
$apply_result = $apply_stmt->get_result();
$apply_stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Job Details</h2>
        
        <div class="job-details">
            <h3>Job Title: <?php echo htmlspecialchars($job['title']); ?></h3>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company']); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
        </div>
        
        <h3>Applicants</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Resume</th>
                <th>Action</th>
            </tr>
            <?php while ($applicant = $apply_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($applicant['name']); ?></td>
                    <td><?php echo htmlspecialchars($applicant['email']); ?></td>
                    <td><a href="../uploads/resumes/<?php echo $applicant['resume']; ?>" target="_blank">View Resume</a></td>
                    <td><a href="view-applicant.php?applicant_id=<?php echo $applicant['applicant_id']; ?>">View</a></td>
                </tr>
            <?php } ?>
        </table>

        <br>
        <a href="manage-jobs.php">Back to Manage Jobs</a>
    </div>
</body>
</html>
