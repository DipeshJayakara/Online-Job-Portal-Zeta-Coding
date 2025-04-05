<?php
session_start();
require_once '../config/connection.php';

// Redirect if admin is not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

// Initialize message variable
$message = '';

// Handle job addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_job'])) {
    // Sanitize and trim input
    $title = trim(htmlspecialchars($_POST['title']));
    $company = trim(htmlspecialchars($_POST['company']));
    $description = trim(htmlspecialchars($_POST['description']));

    // Validate inputs
    if (empty($title) || empty($company) || empty($description)) {
        $message = "All fields are required!";
    } else {
        // Insert job into database
        $query = "INSERT INTO jobs (title, company, description) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $title, $company, $description);

        if ($stmt->execute()) {
            // Redirect to manage-jobs with success flag
            header("Location: manage-jobs.php?added=1");
            exit();
        } else {
            $message = "Failed to add job. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Job</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <style>
        .alert {
            color: #fff;
            background-color: #e74c3c;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Add New Job</h2>

        <!-- Show message if exists -->
        <?php if (!empty($message)): ?>
            <div class="alert"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Job form -->
        <form method="POST">
            <label>Job Title:</label>
            <input type="text" name="title" required>

            <label>Company:</label>
            <input type="text" name="company" required>

            <label>Description:</label>
            <textarea name="description" required></textarea>

            <button type="submit" name="add_job">Add Job</button>
        </form>

        <br>
        <a href="manage-jobs.php">Back to Manage Jobs</a>
    </div>
</body>
</html>
