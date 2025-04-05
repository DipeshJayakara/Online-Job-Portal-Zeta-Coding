<?php
session_start();
require_once '../config/connection.php';

// Redirect if admin is not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

// Fetch the job ID from the URL
if (isset($_GET['id'])) {
    $job_id = $_GET['id'];

    // Fetch the job details
    $query = "SELECT * FROM jobs WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $job = $result->fetch_assoc();
    } else {
        echo "<script>alert('Job not found!'); window.location.href='manage-jobs.php';</script>";
        exit();
    }

    // Fetch the applicants for the job
    $applicantsQuery = "SELECT u.name, u.email, a.application_date FROM applications a 
                        JOIN users u ON a.user_id = u.id 
                        WHERE a.job_id = ?";
    $applicantsStmt = $conn->prepare($applicantsQuery);
    $applicantsStmt->bind_param("i", $job_id);
    $applicantsStmt->execute();
    $applicantsResult = $applicantsStmt->get_result();
} else {
    echo "<script>alert('Invalid job ID!'); window.location.href='manage-jobs.php';</script>";
    exit();
}
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
    <div class="job-details-container">
        <h2>Job Details</h2>

        <div class="job-info">
            <h3><?php echo htmlspecialchars($job['title']); ?></h3>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
            <p><strong>Requirements:</strong> <?php echo nl2br(htmlspecialchars($job['requirements'])); ?></p>
            <p><strong>Posted on:</strong> <?php echo date("F j, Y", strtotime($job['date_posted'])); ?></p>
        </div>

        <h3>Applicants</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Application Date</th>
            </tr>
            <?php while ($applicant = $applicantsResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($applicant['name']); ?></td>
                    <td><?php echo htmlspecialchars($applicant['email']); ?></td>
                    <td><?php echo date("F j, Y", strtotime($applicant['application_date'])); ?></td>
                </tr>
            <?php } ?>
        </table>

        <br>
        <a href="manage-jobs.php">Back to Job Management</a>
    </div>
</body>
</html>
