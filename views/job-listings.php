<?php
session_start();
require_once '../config/connection.php';

// Fetch job listings
$query = "SELECT id, company_name, title, description, salary, location, provider_id, created_at, job_type FROM jobs";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/jobs-listings.css?v=2">
    <title>Job Listings</title>
    <style>
    body { background-color: lightgray; }
</style>

</head>
<body>

    <?php include '../includes/header.php'; ?>  

    <div class="container">
        <h2>Available Jobs</h2>
        <div class="job-list">
            <?php while ($job = mysqli_fetch_assoc($result)) { ?>
                <div class="job-card">
                    <div class="job-title"><?php echo htmlspecialchars($job['title']); ?></div>
                    <div class="job-company"><?php echo htmlspecialchars($job['company_name']); ?></div>
                    <p class="job-description"><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                    <p class="job-meta"><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary']); ?></p>
                    <p class="job-meta"><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                    <a href="job-details.php?job_id=<?php echo $job['id']; ?>" class="apply-btn">View Job</a>
                </div>
            <?php } ?>
        </div>
    </div>


    <a href="dashboard.php">Back to Dashboard</a>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
