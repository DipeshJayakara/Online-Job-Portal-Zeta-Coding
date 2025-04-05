<?php
require_once '../config/connection.php';

$new_password = password_hash("admin", PASSWORD_BCRYPT);
$email = "admin@gmail.com"; // Change if needed

$query = "UPDATE admin SET password = ? WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $new_password, $email);

if ($stmt->execute()) {
    echo "Admin password reset successfully. New password: admin";
} else {
    echo "Error resetting password!";
}

$stmt->close();
$conn->close();
?>
