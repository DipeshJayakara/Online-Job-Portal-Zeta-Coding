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

    // Delete the job from the database
    $delete_query = "DELETE FROM jobs WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $job_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Job deleted successfully!'); window.location.href='manage-jobs.php';</script>";
    } else {
        echo "<script>alert('Error deleting job!'); window.location.href='manage-jobs.php';</script>";
    }
} else {
    echo "<script>alert('Invalid job ID!'); window.location.href='manage-jobs.php';</script>";
}
?>
