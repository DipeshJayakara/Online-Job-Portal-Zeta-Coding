<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo "Unauthorized access.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'delete') {
        $job_id = intval($_POST['job_id']);
        $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->bind_param("i", $job_id);
        if ($stmt->execute()) {
            echo "Job deleted successfully.";
        } else {
            echo "Failed to delete job.";
        }
        $stmt->close();

    } elseif ($action === 'update') {
        $job_id = intval($_POST['job_id']);
        $title = trim($_POST['title']);
        $company = trim($_POST['company']);
        $description = trim($_POST['description']);

        $stmt = $conn->prepare("UPDATE jobs SET title = ?, company = ?, description = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $company, $description, $job_id);
        if ($stmt->execute()) {
            echo "Job updated successfully.";
        } else {
            echo "Failed to update job.";
        }
        $stmt->close();
    } else {
        echo "Invalid action.";
    }
} else {
    echo "Invalid request.";
}
