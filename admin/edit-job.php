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

    // Fetch job details from the database
    $query = "SELECT * FROM jobs WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $job = $result->fetch_assoc();
    
    // If the job doesn't exist
    if (!$job) {
        echo "<script>alert('Job not found!'); window.location.href='manage-jobs.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid job ID!'); window.location.href='manage-jobs.php';</script>";
    exit();
}

// Handle job update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $company = trim($_POST['company']);
    $location = trim($_POST['location']);
    $description = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);

    // Validate the input
    if (empty($title) || empty($company) || empty($location) || empty($description) || empty($requirements)) {
        echo "<script>alert('All fields are required!');</script>";
    } else {
        // Update the job details in the database
        $update_query = "UPDATE jobs SET title = ?, company = ?, location = ?, description = ?, requirements = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssssi", $title, $company, $location, $description, $requirements, $job_id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Job updated successfully!'); window.location.href='view-job.php?id=$job_id';</script>";
        } else {
            echo "<script>alert('Error updating job!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Edit Job</h2>
        
        <form method="POST">
            <label for="title">Job Title:</label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($job['title']); ?>" required>
            
            <label for="company">Company:</label>
            <input type="text" name="company" id="company" value="<?php echo htmlspecialchars($job['company']); ?>" required>
            
            <label for="location">Location:</label>
            <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($job['location']); ?>" required>
            
            <label for="description">Job Description:</label>
            <textarea name="description" id="description" required><?php echo htmlspecialchars($job['description']); ?></textarea>
            
            <label for="requirements">Job Requirements:</label>
            <textarea name="requirements" id="requirements" required><?php echo htmlspecialchars($job['requirements']); ?></textarea>
            
            <button type="submit">Update Job</button>
        </form>

        <br>
        <a href="view-job.php?id=<?php echo $job['id']; ?>">Back to Job Details</a>
    </div>
</body>
</html>
