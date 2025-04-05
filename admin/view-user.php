    <?php
    session_start();
    require_once '../config/connection.php';

    if (!isset($_SESSION['admin_logged_in'])) {
        header('Location: admin-login.php');
        exit();
    }

    if (!isset($_GET['id'])) {
        echo "User ID not provided.";
        exit();
    }

    $user_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "User not found.";
        exit();
    }

    $user = $result->fetch_assoc();
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>View User</title>
        <link rel="stylesheet" href="../assets/css/admin-style.css">
    </head>
    <body>
        <div class="view-user-container">
            <h2>User Details</h2>
            <p><strong>ID:</strong> <?php echo $user['id']; ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            <br>
            <a href="manage-users.php">Back to Manage Users</a>
        </div>
    </body>
    </html>
