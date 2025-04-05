<?php
session_start();
require_once '../config/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'delete') {
    $job_id = intval($_POST['job_id'] ?? 0);
    if ($job_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid job ID.']);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
    $stmt->bind_param("i", $job_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Job deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete job.']);
    }
    $stmt->close();

} elseif ($action === 'update') {
    $job_id = intval($_POST['job_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($job_id <= 0 || empty($title) || empty($company) || empty($description)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit();
    }

    $stmt = $conn->prepare("UPDATE jobs SET title = ?, company = ?, description = ? WHERE id = ?");
    $stmt->bind_param("sssi", $title, $company, $description, $job_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Job updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update job.']);
    }
    $stmt->close();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
}
?>
