<head>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/footer.css">
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
                <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                    <img src="<?php echo BASE_URL; ?>assets/images/facebook-icon.png" alt="Facebook">
                </a>
                <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                    <img src="<?php echo BASE_URL; ?>assets/images/twitter-icon.png" alt="Twitter">
                </a>
                <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                    <img src="<?php echo BASE_URL; ?>assets/images/linkedin-icon.png" alt="LinkedIn">
                </a>
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
