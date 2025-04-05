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
    define("BASE_URL", "http://localhost/Internship-ZetaCoding/Online%20job%20portal/");
}

// Environment: 'development' or 'production'
define("ENVIRONMENT", "development"); 

// Enable error reporting for debugging in development mode
if (ENVIRONMENT === "development") {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
?>
