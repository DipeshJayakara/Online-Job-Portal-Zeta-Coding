<?php
session_start();
require_once('../config/connection.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit;
}

$current_username = $_SESSION['admin_username'];
$success = "";
$error = "";

// Update username
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST['username']);

    if (!empty($new_username)) {
        $stmt = $conn->prepare("UPDATE admin SET username = ? WHERE username = ?");
        $stmt->bind_param("ss", $new_username, $current_username);

        if ($stmt->execute()) {
            $_SESSION['admin_username'] = $new_username;
            $success = "Username updated successfully.";
            $current_username = $new_username;
        } else {
            $error = "Error updating username.";
        }
    } else {
        $error = "Username cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <div class="admin-header">
        <h1>Admin Profile</h1>
        <a href="admin-dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>

    <div class="profile-form">
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <label for="username">Username:</label><br>
            <input type="text" name="username" value="<?php echo htmlspecialchars($current_username); ?>" required><br><br>
            <button type="submit">Update Username</button>
        </form>

        <br>
        <a href="change-password.php" class="btn">Change Password</a>
    </div>
</body>
</html>
