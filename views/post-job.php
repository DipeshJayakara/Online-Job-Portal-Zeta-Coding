<?php
require_once '../config/connection.php';
require_once '../includes/auth.php'; // Handles session start

// ✅ Check if user is authenticated and is a Job Provider
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Get form data and sanitize inputs
    $company_name = trim($_POST['company_name']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $job_type = trim($_POST['job-type']); // This matches your form's name attribute
    $salary = trim($_POST['salary']);
    $location = trim($_POST['location']);
    $provider_id = $_SESSION['user_id'];

    // ✅ Use Prepared Statements to prevent SQL Injection
    // Use backticks to escape the `job-type` column
    $stmt = $conn->prepare("INSERT INTO jobs (company_name, title, description, salary, location, `job_type`, provider_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $company_name, $title, $description, $salary, $location, $job_type, $provider_id);

    if ($stmt->execute()) {
        header('Location: dashboard.php?success=job_posted');
        exit();
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job</title>
    <link rel="stylesheet" href="../assets/css/post-job.css">
</head>
<body>
    <h2>Post a New Job</h2>
    
    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Company Name:</label>
        <input type="text" name="company_name" required>

        <label>Job Title:</label>
        <input type="text" name="title" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>

        <label>Job Type:</label>
        <input type="text" name="job-type" required> <!-- Corrected to use job-type -->

        <label>Salary:</label>
        <input type="text" name="salary" required>

        <label>Location:</label>
        <input type="text" name="location" required>

        <button type="submit">Post Job</button>
    </form>
    
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
