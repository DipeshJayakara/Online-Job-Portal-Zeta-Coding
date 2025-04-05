<?php
session_start();
require_once '../config/connection.php';

// Redirect if admin is not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Handle password change request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate input
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required!');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>alert('New passwords do not match!');</script>";
    } else {
        // Fetch current password from the database
        $stmt = $conn->prepare("SELECT password FROM admin WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();

        if ($admin && password_verify($current_password, $admin['password'])) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_password, $admin_id);
            if ($update_stmt->execute()) {
                echo "<script>alert('Password updated successfully!'); window.location.href='admin-dashboard.php';</script>";
            } else {
                echo "<script>alert('Error updating password!');</script>";
            }
        } else {
            echo "<script>alert('Current password is incorrect!');</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Change Password</h2>
        
        <form method="POST">
            <label>Current Password:</label>
            <input type="password" name="current_password" required>
            
            <label>New Password:</label>
            <input type="password" name="new_password" required>
            
            <label>Confirm New Password:</label>
            <input type="password" name="confirm_password" required>
            
            <button type="submit" name="update_password">Update Password</button>
        </form>
        
        <br>
        <a href="admin-dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
