<?php
session_start();
require_once '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['reset_email'] = $email; // Store email in session
        header("Location: reset-password.php"); // Redirect
        exit();
    } else {
        echo "<script>alert('No account found with this email.'); window.location.href='forgot-password.php';</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" required>
        <button type="submit">Proceed</button>
    </form>
</body>
</html>
