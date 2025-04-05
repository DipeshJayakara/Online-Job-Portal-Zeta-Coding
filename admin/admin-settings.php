<?php
session_start();
require_once '../config/connection.php';

// Redirect if admin is not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Fetch current admin details (username + password)
$query = "SELECT username, password FROM admin WHERE id = ?";
$fetch_stmt = $conn->prepare($query);
$fetch_stmt->bind_param("i", $admin_id);
$fetch_stmt->execute();
$result = $fetch_stmt->get_result();
$admin = $result->fetch_assoc();

// Handle username update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);

    if (empty($username)) {
        echo "<script>alert('Username is required!');</script>";
    } else {
        $update_stmt = $conn->prepare("UPDATE admin SET username = ? WHERE id = ?");
        $update_stmt->bind_param("si", $username, $admin_id);
        if ($update_stmt->execute()) {
            echo "<script>alert('Username updated successfully!'); window.location.href='admin-settings.php';</script>";
        } else {
            echo "<script>alert('Error updating username!');</script>";
        }
    }
}

// Handle password update
if (isset($_POST['update_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo "<script>alert('All password fields are required!');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>alert('New passwords do not match!');</script>";
    } elseif (!password_verify($current_password, $admin['password'])) {
        echo "<script>alert('Current password is incorrect!');</script>";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $update_pass_stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
        $update_pass_stmt->bind_param("si", $hashed_password, $admin_id);
        if ($update_pass_stmt->execute()) {
            echo "<script>alert('Password updated successfully!'); window.location.href='admin-settings.php';</script>";
        } else {
            echo "<script>alert('Error updating password!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Admin Profile & Settings</h2>

        <!-- Profile Update Form -->
        <form method="POST">
            <h3>Update Username</h3>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <br>

        <!-- Password Update Form -->
        <form method="POST">
            <h3>Change Password</h3>
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" id="current_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" required>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <button type="submit" name="update_password">Update Password</button>
        </form>

        <br>
        <a href="admin-dashboard.php">← Back to Dashboard</a>
    </div>
</body>
</html>
