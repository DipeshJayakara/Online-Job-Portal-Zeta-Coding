<?php
session_start();
require_once '../config/connection.php';
require_once '../ats-score.php'; // Include ATS scoring script

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=session_expired');
    exit();
}

$applicant_id = $_SESSION['user_id'];

// ✅ Validate CSRF Token
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['job_id']) || !isset($_POST['csrf_token'])) {
    header('Location: job-listings.php?error=invalid_request');
    exit();
}

if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF validation failed.");
}

// ✅ Sanitize job_id
$job_id = intval($_POST['job_id']);
if ($job_id <= 0) {
    header('Location: job-listings.php?error=invalid_job_id');
    exit();
}

// ✅ Check if job exists
$stmt = $conn->prepare("SELECT id FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$jobExists = $stmt->get_result()->num_rows > 0;
$stmt->close();

if (!$jobExists) {
    header('Location: job-listings.php?error=job_not_found');
    exit();
}

// ✅ Fetch applicant's resume
$resumeQuery = $conn->prepare("SELECT resume FROM users WHERE id = ?");
$resumeQuery->bind_param("i", $applicant_id);
$resumeQuery->execute();
$resumeResult = $resumeQuery->get_result();
$resumeData = $resumeResult->fetch_assoc();
$resumeQuery->close();

if (!$resumeData || empty($resumeData['resume'])) {
    die("Error: Resume not found. Please upload your resume.");
}

$resumePath = "../uploads/resumes/" . $resumeData['resume'];

// ✅ Calculate ATS Score
$atsScore = calculate_ats_score($resumePath);

// ✅ Insert application with ATS score
$stmt = $conn->prepare("INSERT INTO applications (job_id, applicant_id, ats_score, status) VALUES (?, ?, ?, 'Pending')");
$stmt->bind_param("iid", $job_id, $applicant_id, $atsScore);

if ($stmt->execute()) {
    header("Location: job-details.php?job_id=$job_id&success=applied");
    exit();
} else {
    error_log("Error applying for job: " . $stmt->error);
    die("Something went wrong. Please try again.");
}

$stmt->close();
?>
