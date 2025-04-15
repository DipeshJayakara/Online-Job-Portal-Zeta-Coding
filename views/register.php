<?php
session_start();
require_once '../config/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);  // ✅ Correct variable
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role']; // 'seeker' or 'provider'

    // ✅ Insert into Database without Resume
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);  // ✅ Using $name

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['role'] = $role;
        header("Location: dashboard.php?success=registered");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/css/sign.css?v=<?= time(); ?>">
    <title>Register</title>
</head>
<body>
    <form action="register.php" method="POST">
        <h2>Register</h2>
        <input type="text" name="name" required placeholder="Full Name">
        <input type="email" name="email" required placeholder="Email">
        <input type="password" name="password" required placeholder="Password">
        <select name="role">
            <option value="seeker">Job Seeker</option>
            <option value="provider">Job Provider</option>
        </select>
        <button type="submit">Register</button>
    </form>
</body>
</html>
