<?php
session_start();
require_once '../config/connection.php';

// Check if email is stored in session
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot-password.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['reset_email'];
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
    $stmt->bind_param("ss", $new_password, $email);
    
    if ($stmt->execute()) {
        echo "<script>alert('Password reset successfully. Please login now.'); window.location.href='login.php';</script>";
        session_destroy();
    } else {
        echo "<script>alert('Something went wrong. Try again.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <form method="POST">
        <label>New Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Update Password</button>
    </form>
</body>
</html>
