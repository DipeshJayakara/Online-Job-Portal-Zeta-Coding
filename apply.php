<?php
session_start();
require_once 'config/connection.php';

// ✅ Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: views/login.php?error=session_expired');
    exit();
}

$applicant_id = $_SESSION['user_id'];

// ✅ Validate Request Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: views/job-listings.php?error=invalid_request');
    exit();
}

// ✅ Validate Job ID and CSRF Token
if (!isset($_POST['job_id']) || !isset($_POST['csrf_token'])) {
    die("Missing job ID or CSRF token.");
}

if (!isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF validation failed. Please refresh the page and try again.");
}

// ✅ Sanitize and validate job_id
$job_id = intval($_POST['job_id']);
if ($job_id <= 0) {
    header('Location: views/job-listings.php?error=invalid_job_id');
    exit();
}

// ✅ Check if the job exists
$stmt = $conn->prepare("SELECT id FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$jobExists = $stmt->get_result()->num_rows > 0;
$stmt->close();

if (!$jobExists) {
    header('Location: views/job-listings.php?error=job_not_found');
    exit();
}

// ✅ Check if user already applied
$stmt = $conn->prepare("SELECT id FROM applications WHERE job_id = ? AND applicant_id = ?");
$stmt->bind_param("ii", $job_id, $applicant_id);
$stmt->execute();
$alreadyApplied = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($alreadyApplied) {
    header("Location: views/job-details.php?job_id=$job_id&error=already_applied");
    exit();
}

// ✅ Fetch applicant's resume (text-based, NOT a file)
$resumeQuery = $conn->prepare("SELECT resume FROM users WHERE id = ?");
$resumeQuery->bind_param("i", $applicant_id);
$resumeQuery->execute();
$resumeResult = $resumeQuery->get_result();
$resumeData = $resumeResult->fetch_assoc();
$resumeQuery->close();

if (!$resumeData || empty($resumeData['resume'])) {
    // Redirect to resume upload page
    header("Location: views/upload-resume.php?error=no_resume");
    exit();
}

$resumeText = $resumeData['resume']; // Resume is stored as text

// ✅ Calculate ATS Score based on text resume
require_once 'ats-checker.php';
$atsScore = calculate_ats_score($resumeText);

// ✅ Insert application with ATS score
$stmt = $conn->prepare("INSERT INTO applications (job_id, applicant_id, ats_score, status) VALUES (?, ?, ?, 'Pending')");
$stmt->bind_param("iid", $job_id, $applicant_id, $atsScore);

if ($stmt->execute()) {
    header("Location: views/job-details.php?job_id=$job_id&success=applied");
    exit();
} else {
    error_log("Error applying for job: " . $stmt->error);
    die("Something went wrong. Please try again.");
}

$stmt->close();
?>
