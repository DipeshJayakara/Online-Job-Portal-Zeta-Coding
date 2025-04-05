<?php
session_start();
require_once __DIR__ . '/config/connection.php'; // ✅ Ensuring correct path
require_once __DIR__ . '/includes/auth.php'; // ✅ Ensuring correct path

// ✅ Check if user is a job provider
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'provider') {
    header('Location: views/login.php');
    exit();
}

// ✅ Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Sanitize and validate inputs
    $company_name = trim($_POST['company_name']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $salary = trim($_POST['salary']);
    $location = trim($_POST['location']);
    $provider_id = $_SESSION['user_id'];

    // ✅ Check if the database connection exists
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // ✅ Prepare & execute query securely
    $stmt = $conn->prepare("INSERT INTO jobs (company_name, title, description, salary, location, provider_id) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        die("Something went wrong.");
    }

    $stmt->bind_param("sssssi", $company_name, $title, $description, $salary, $location, $provider_id);
    if ($stmt->execute()) {
        header('Location: views/dashboard.php?success=job_posted');
        exit();
    } else {
        error_log("Error inserting job: " . $stmt->error);
        die("Failed to post job.");
    }

    $stmt->close();
}
?>
