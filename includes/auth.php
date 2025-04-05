<?php
session_start();

// Regenerate session ID to prevent fixation attacks
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login.php");
    exit();
}

session_regenerate_id(true);
?>
