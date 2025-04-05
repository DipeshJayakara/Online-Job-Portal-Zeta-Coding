<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    $user_id = intval($_GET['delete_id']);

    // Optional safeguard: prevent deleting self/admin
    if ($_SESSION['admin_id'] == $user_id) {
        echo "<script>alert('You cannot delete your own account.'); window.location.href='user-management.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    echo "<script>alert('User deleted successfully!'); window.location.href='user-management.php';</script>";
    exit();
}

// Handle status toggle
if (isset($_GET['change_status_id']) && isset($_GET['status'])) {
    $user_id = intval($_GET['change_status_id']);
    $new_status = $_GET['status'] === 'active' ? 'inactive' : 'active';

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $user_id);
    $stmt->execute();
    echo "<script>alert('User status updated!'); window.location.href='user-management.php';</script>";
    exit();
}

// Fetch all users securely
$stmt = $conn->prepare("SELECT id, email, role, status FROM users");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <style>
        .status-active { color: green; font-weight: bold; }
        .status-inactive { color: red; font-weight: bold; }
        .action-links a { margin-right: 8px; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>User Management</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td class="<?php echo $user['status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo ucfirst($user['status']); ?>
                        </td>
                        <td class="action-links">
                            <a href="edit-user.php?id=<?php echo $user['id']; ?>">Edit</a>
                            <a href="?delete_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            <a href="?change_status_id=<?php echo $user['id']; ?>&status=<?php echo $user['status']; ?>">
                                <?php echo $user['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <br>
        <a href="admin-dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
