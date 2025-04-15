<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit;
}

$username = $_SESSION['admin_username'];
$success = "";
$error = "";

// Handle password change
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch admin record
    $stmt = $conn->prepare("SELECT password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Verify current password
    if (password_verify($current_password, $hashed_password)) {
        if ($new_password === $confirm_password) {
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE admin SET password = ? WHERE username = ?");
            $update->bind_param("ss", $new_hashed, $username);
            if ($update->execute()) {
                $success = "Password changed successfully.";
            } else {
                $error = "Failed to update password.";
            }
        } else {
            $error = "New passwords do not match.";
        }
    } else {
        $error = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Admin Password</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <div class="admin-header">
        <h1>Change Password</h1>
        <a href="admin-dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>

    <div class="profile-form">
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <label>Current Password:</label><br>
            <input type="password" name="current_password" required><br><br>

            <label>New Password:</label><br>
            <input type="password" name="new_password" required><br><br>

            <label>Confirm New Password:</label><br>
            <input type="password" name="confirm_password" required><br><br>

            <button type="submit">Change Password</button>
        </form>
    </div>
</body>
</html>
