<?php
session_start();
require_once __DIR__ . '/config/connection.php';  // Corrected path

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    if (!$stmt) {
        die("Error in preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Check if user exists and verify password
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true); // Security feature

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Log successful login
        error_log("Login Successful: User ID - " . (int)$_SESSION['user_id'] . ", Role - " . htmlspecialchars($_SESSION['role']));

        // Redirect user based on role
        header('Location: ./views/dashboard.php');
        exit();
    } else {
        error_log("Failed login attempt for email: " . htmlspecialchars(substr($email, 0, 3)) . "***");

        echo "<script>alert('Invalid credentials. Please try again.'); window.location.href='login.php';</script>";
        exit();
    }
}
?>
