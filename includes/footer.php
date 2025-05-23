<head>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h3>About Us</h3>
            <p>Your go-to platform for finding the perfect job or the best candidate.</p>
        </div>

        <div class="footer-section">
            <h3>Quick Links</h3>
            <nav>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>views/job-listings.php">Jobs</a></li>
                    <li><a href="<?php echo BASE_URL; ?>views/contact.php">Contact</a></li>
                    <li><a href="<?php echo BASE_URL; ?>views/register.php">Sign Up</a></li>
                    <li><a href="<?php echo BASE_URL; ?>views/login.php">Login</a></li>
                </ul>
            </nav>
        </div>

        <div class="footer-section">
            <h3>Follow Us</h3>
            <div class="social-icons">
                <a href="https://facebook.com" class="fa fa-facebook"></a>
                <a href="https://twitter.com" class="fa fa-twitter"></a>
                <a href="https://linkedin.com" class="fa fa-linkedin"></a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
    </div>
</footer>

<!-- Add JavaScript file -->
<script src="<?php echo BASE_URL; ?>assets/js/footer.js"></script>
</body>
</html>
