<?php
session_start();
require_once '../config/connection.php';

// Verify the reset token and process the password change
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $reset_token = $_GET['token'];

    // Check if the token exists in the database
    $stmt = $conn->prepare("SELECT id FROM admin WHERE reset_token = ?");
    $stmt->bind_param("s", $reset_token);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();

    if ($admin) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = trim($_POST['new_password']);
            $confirm_password = trim($_POST['confirm_password']);

            if (empty($new_password) || empty($confirm_password)) {
                echo "<script>alert('Both fields are required!');</script>";
            } elseif ($new_password !== $confirm_password) {
                echo "<script>alert('Passwords do not match!');</script>";
            } else {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // Update the password and reset the token to null
                $stmt = $conn->prepare("UPDATE admin SET password = ?, reset_token = NULL WHERE reset_token = ?");
                $stmt->bind_param("ss", $hashed_password, $reset_token);
                if ($stmt->execute()) {
                    echo "<script>alert('Password has been reset successfully!'); window.location.href='admin-login.php';</script>";
                } else {
                    echo "<script>alert('Error resetting password. Please try again later.');</script>";
                }
                $stmt->close();
            }
        }
    } else {
        echo "<script>alert('Invalid or expired reset token.'); window.location.href='admin-login.php';</script>";
    }
} else {
    echo "<script>alert('No reset token found.'); window.location.href='admin-login.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>
    <div class="reset-password-container">
        <h2>Reset Your Password</h2>

        <form method="POST">
            <label>New Password:</label>
            <input type="password" name="new_password" placeholder="Enter new password" required>
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            <button type="submit">Reset Password</button>
        </form>

        <br>
        <a href="admin-login.php">Back to Login</a>
    </div>
</body>
</html>
