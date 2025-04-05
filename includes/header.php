<?php 
require_once __DIR__ . '/../config/config.php'; 

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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/header.css">
</head>
<body>

<header>
    <div class="nav-container">
        <div class="logo">
            <a href="<?php echo BASE_URL; ?>index.php"><?php echo SITE_NAME; ?></a>
        </div>

        <nav>
            <ul class="nav-links">
                <li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>views/job-listings.php">Jobs</a></li>
                <li><a href="<?php echo BASE_URL; ?>views/register.php">Register</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="<?php echo BASE_URL; ?>views/dashboard.php">Dashboard</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- ✅ Account Section (Right Side) -->
        <div class="account-section">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="account-dropdown">
                    <button class="account-btn" onclick="toggleAccountMenu()">My Account ▼</button>
                    <div class="dropdown-menu" id="account-menu">
                        <a href="<?php echo BASE_URL; ?>views/account.php">View Profile</a>
                        <a href="<?php echo BASE_URL; ?>logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>views/login.php" class="login-btn">Login</a>
            <?php endif; ?>
        </div>

        <div class="menu-toggle" onclick="toggleMenu()" aria-label="Toggle navigation">☰</div>
    </div>
</header>

<script src="<?php echo BASE_URL; ?>assets/js/header.js"></script>
                