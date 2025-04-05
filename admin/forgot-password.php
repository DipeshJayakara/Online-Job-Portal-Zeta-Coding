<?php
session_start();
require_once '../config/connection.php';

// Check if reset token is provided
if (!isset($_GET['token']) || empty($_GET['token'])) {
    echo "<script>alert('Invalid or missing token.'); window.location.href='admin-login.php';</script>";
    exit();
}

$token = $_GET['token'];

// Fetch admin details using the reset token
$stmt = $conn->prepare("SELECT id FROM admin WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if (!$admin) {
    echo "<script>alert('Invalid or expired token.'); window.location.href='admin-login.php';</script>";
    exit();
}

$admin_id = $admin['id'];

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "<script>alert('Invalid request.'); window.location.href='admin-login.php';</script>";
        exit();
    }

    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($new_password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required.');</script>";
    } elseif (strlen($new_password) < 8) {
        echo "<script>alert('Password must be at least 8 characters long.');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE admin SET password = ?, reset_token = NULL WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $admin_id);

        if ($stmt->execute()) {
            unset($_SESSION['csrf_token']);
            echo "<script>alert('Password updated successfully!'); window.location.href='admin-login.php';</script>";
        } else {
            echo "<script>alert('Error updating password. Please try again.');</script>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <style>
        .toggle-password {
            cursor: pointer;
            font-size: 12px;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="reset-password-container">
        <h2>Reset Password</h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" required>
            <span class="toggle-password" onclick="toggleVisibility('new_password')">Show</span>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
            <span class="toggle-password" onclick="toggleVisibility('confirm_password')">Show</span>

            <button type="submit">Update Password</button>
        </form>
        <br>
        <a href="admin-login.php">Back to Login</a>
    </div>

    <script>
        function toggleVisibility(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
