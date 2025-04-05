<?php
// auth.php - Handles authentication for admin and user sessions

// Start the session
session_start();

// Function to check if the user is logged in as an admin
function isAdminLoggedIn() {
    // Check if the admin session is set
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Function to check if the user is logged in as a regular user
function isUserLoggedIn() {
    // Check if the user session is set
    return isset($_SESSION['user_id']);
}

// Admin Authentication - Protects admin pages
function protectAdminPage() {
    if (!isAdminLoggedIn()) {
        // If not logged in, redirect to the login page
        header('Location: ../admin/admin-login.php');
        exit();
    }
}

// User Authentication - Protects user pages (can be used similarly for other user-based pages)
function protectUserPage() {
    if (!isUserLoggedIn()) {
        // If not logged in, redirect to the login page
        header('Location: ../views/login.php');
        exit();
    }
}

// Logout function for admin users
function adminLogout() {
    session_start();
    // Unset session variables
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to login page
    header("Location: admin-login.php");
    exit();
}

// Logout function for normal users
function userLogout() {
    session_start();
    // Unset session variables
    session_unset();
    // Destroy the session
    session_destroy();
    // Redirect to login page
    header("Location: login.php");
    exit();
}
?>
