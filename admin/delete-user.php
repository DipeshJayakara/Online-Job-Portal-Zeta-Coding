<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['user_id'], $_POST['csrf_token'])) {
    die('Invalid request.');
}

if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF token mismatch.');
}

$user_id = intval($_POST['user_id']);

// Check if user exists and isn't an admin
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<script>alert('User not found.'); window.location.href='manage-users.php';</script>";
    exit();
}
$user = $result->fetch_assoc();
if ($user['role'] === 'admin') {
    echo "<script>alert('You cannot delete an admin account.'); window.location.href='manage-users.php';</script>";
    exit();
}

// Delete the user
$delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$delete_stmt->bind_param("i", $user_id);
if ($delete_stmt->execute()) {
    echo "<script>alert('User deleted successfully.'); window.location.href='manage-users.php';</script>";
} else {
    echo "<script>alert('Failed to delete user.'); window.location.href='manage-users.php';</script>";
}
?>
