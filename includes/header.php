<?php 
include 'config/config.php'; 

// Check if a session is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> | Job Portal</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <div class="nav-container">
        <div class="logo">
            <a href="index.php"><?php echo SITE_NAME; ?></a>
        </div>

        <nav>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="views/job-listings.php">Jobs</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="views/dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php" class="logout-btn">Logout</a></li>
                <?php else: ?>
                    <li><a href="views/register.php">Register</a></li>
                    <li><a href="views/login.php" class="login-btn">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="menu-toggle" onclick="toggleMenu()">☰</div> <!-- Mobile Menu Toggle -->
    </div>
</header>

<script>
    function toggleMenu() {
        document.querySelector('.nav-links').classList.toggle('active');
    }
</script>
