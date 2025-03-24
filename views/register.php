<?php include '../config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/sign.css">
</head>
<body>
    <form action="../process-registration.php" method="POST">
        <h2>Register</h2>
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role">
            <option value="job_seeker">Job Seeker</option>
            <option value="job_provider">Job Provider</option>
        </select>
        <button type="submit">Register</button>
    </form>
</body>
</html>
