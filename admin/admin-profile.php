<?php
require_once 'admin-auth.php';
require_once '../config/connection.php';

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$query = "SELECT * FROM admin WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_username = trim($_POST['username']);
    $new_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $current_password = trim($_POST['current_password']);

    // Validate current password
    if (!password_verify($current_password, $admin['password'])) {
        echo "<script>alert('Current password is incorrect!');</script>";
    } else {
        if (!empty($new_password) || !empty($confirm_password)) {
            if ($new_password !== $confirm_password) {
                echo "<script>alert('New passwords do not match!');</script>";
            } else {
                // Update username and password
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $update_query = "UPDATE admin SET username = ?, password = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("ssi", $new_username, $hashed_password, $admin_id);
                $success = $update_stmt->execute();
            }
        } else {
            // Update only username
            $update_query = "UPDATE admin SET username = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("si", $new_username, $admin_id);
            $success = $update_stmt->execute();
        }

        if (isset($success) && $success) {
            echo "<script>alert('Profile updated successfully!'); window.location.href='admin-profile.php';</script>";
        } else {
            echo "<script>alert('Error updating profile!');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>
    <div class="dashboard-container">
        <h2>Admin Profile</h2>
        <form method="POST">
            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username'], ENT_QUOTES); ?>" required>
            </div>
            <div>
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" required>
            </div>
            <div>
                <label for="password">New Password: <small>(leave blank to keep current)</small></label>
                <input type="password" name="password">
            </div>
            <div>
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password">
            </div>
            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <br>
        <a href="admin-dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
