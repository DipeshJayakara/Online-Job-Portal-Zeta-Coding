<?php
session_start();
require_once '../config/connection.php';
require_once '../includes/auth.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../views/login.php');
    exit();
}

// Approve provider
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $id = intval($_POST['approve']);
        $stmt = $conn->prepare("UPDATE job_providers SET verified = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    // Reject provider
    if (isset($_POST['reject'])) {
        $id = intval($_POST['reject']);
        $stmt = $conn->prepare("DELETE FROM job_providers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    header("Location: verify-providers.php");
    exit();
}

// Fetch unverified providers
$stmt = $conn->prepare("SELECT id, name, email FROM job_providers WHERE verified = 0");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Job Providers</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }
        form {
            display: inline-block;
        }
        button {
            padding: 5px 10px;
            margin: 0 2px;
            cursor: pointer;
        }
        .approve-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
        }
        .reject-btn {
            background-color: #f44336;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Verify Job Providers</h2>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($provider = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($provider['name']); ?></td>
                    <td><?php echo htmlspecialchars($provider['email']); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="approve" value="<?php echo $provider['id']; ?>">
                            <button type="submit" class="approve-btn">Approve</button>
                        </form>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to reject this provider?');">
                            <input type="hidden" name="reject" value="<?php echo $provider['id']; ?>">
                            <button type="submit" class="reject-btn">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3" style="text-align:center;">No providers waiting for verification.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <div style="text-align:center;">
        <a href="admin-dashboard.php">⬅ Back to Dashboard</a>
    </div>
</body>
</html>
