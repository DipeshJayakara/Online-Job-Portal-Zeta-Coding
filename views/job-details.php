<?php
session_start();
require_once '../config/connection.php';

// ✅ Ensure user is logged in before accessing job details
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=login_required');
    exit();
}

$applicant_id = $_SESSION['user_id'];

// ✅ Validate job_id from URL
if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
    header('Location: job-listings.php?error=invalid_id');
    exit();
}

$job_id = intval($_GET['job_id']);

// ✅ Fetch job details securely
$stmt = $conn->prepare("SELECT company_name, title, description, salary, location FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();
$stmt->close();

// ✅ Redirect if job not found
if (!$job) {
    header('Location: job-listings.php?error=notfound');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details</title>
    <link rel="stylesheet" href="../assets/css/job-details.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="job-container">
        <div class="job-box">
            <h2><?php echo htmlspecialchars($job['title']); ?></h2>

            <div class="job-details">
                <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
            </div>

            <form action="../apply.php" method="POST">
                <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit">Apply Now</button>
            </form>

            <a href="job-listings.php" class="back-link">Back to Job Listings</a>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
