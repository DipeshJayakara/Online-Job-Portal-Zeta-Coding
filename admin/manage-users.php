<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit;
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $id");
    header("Location: manage-users.php");
    exit;
}

// Handle verification toggle
if (isset($_GET['verify'])) {
    $id = intval($_GET['verify']);
    $conn->query("UPDATE users SET verified = 1 WHERE id = $id");
    header("Location: manage-users.php");
    exit;
}

// Fetch users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <div class="admin-header">
        <h1>Manage Users</h1>
        <a href="admin-dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name / Email</th>
                <th>Role</th>
                <th>Verified</th>
                <th>Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $users->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['name']) . "<br>" . htmlspecialchars($user['email']); ?></td>
                <td><?php echo ucfirst($user['role']); ?></td>
                <td><?php echo $user['verified'] ? '✅' : '❌'; ?></td>
                <td><?php echo date("Y-m-d", strtotime($user['created_at'])); ?></td>
                <td>
                    <?php if (!$user['verified']) { ?>
                        <a href="?verify=<?php echo $user['id']; ?>" class="btn verify">Verify</a>
                    <?php } ?>
                    <a href="?delete=<?php echo $user['id']; ?>" class="btn delete" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
