<?php
// Prevent multiple session_start() calls
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define global constants safely
if (!defined("SITE_NAME")) {
    define("SITE_NAME", "Job Portal");
}

if (!defined("BASE_URL")) {
    define("BASE_URL", "http://localhost/job-portal/");
}

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
