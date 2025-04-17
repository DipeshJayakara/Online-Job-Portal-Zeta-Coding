<?php
session_start();
require_once __DIR__ . '/../config/connection.php'; // Database connection

// Redirect if the user is not authenticated
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?= time(); ?>">
    <style>
        .user-dashboard-wrapper {
            padding: 30px;
            background: #f8f9fa;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
            color: #333;
        }

        .dashboard-sections {
            display: flex;
            flex-direction: column;
            gap: 30px;
            max-width: 900px;
            margin: auto;
        }

        .dashboard-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px 30px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .dashboard-card h3 {
            font-size: 22px;
            color: #444;
            margin-bottom: 15px;
        }

        .dashboard-card h4 {
            font-size: 18px;
            margin-top: 20px;
            color: #555;
        }

        .dashboard-link {
            display: inline-block;
            margin: 8px 0;
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .dashboard-link:hover {
            color: #0056b3;
        }

        .application-list ul,
        .job-list ul {
            margin-top: 10px;
            padding-left: 20px;
        }

        .application-list li,
        .job-list li {
            margin-bottom: 6px;
        }

        .application-list a,
        .job-list a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
        }

        .application-list a:hover,
        .job-list a:hover {
            color: #007bff;
        }

        /* Distinct styling by role */
        .seeker-section {
            border-left: 5px solid #17a2b8;
        }

        .provider-section {
            border-left: 5px solid #28a745;
        }

        .admin-section {
            border-left: 5px solid #ffc107;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .dashboard-card {
                padding: 20px;
            }

            .dashboard-title {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    <?php include '../includes/header.php'; ?>

    <div class="theme-toggle">
        <label class="switch">
            <input type="checkbox" id="themeSwitch">
            <span class="slider round"></span>
        </label>
        <span class="toggle-label">Dark Mode</span>
    </div>


    <div class="user-dashboard-wrapper">
        <h2 class="dashboard-title">Welcome to Your Dashboard</h2>

        <!-- Dashboard Content -->
        <div class="dashboard-sections">

            <?php if ($user_role === 'seeker') { ?>
                <section class="dashboard-card seeker-section">
                    <h3>Actions for Job Seekers</h3>
                    <a class="dashboard-link" href="job-listings.php">Browse Available Jobs</a>

                    <h4>Your Applications</h4>
                    <div class="application-list">
                    <?php
                    $stmt = $conn->prepare("SELECT jobs.id, jobs.title FROM applications JOIN jobs ON applications.job_id = jobs.id WHERE applications.applicant_id = ?");
                    $stmt->bind_param("i", $user_id);

                    if ($stmt->execute()) {
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            echo "<ul>";
                            while ($job = $result->fetch_assoc()) {
                                echo "<li><a href='job-details.php?job_id=" . $job['id'] . "'>" . htmlspecialchars($job['title']) . "</a></li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "<p>No applications yet.</p>";
                        }
                    } else {
                        echo "<p>Error: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                    ?>
                    </div>
                </section>
            <?php } ?>

            <?php if ($user_role === 'provider') { ?>
                <section class="dashboard-card provider-section">
                    <h3>Actions for Job Providers</h3>
                    <a class="dashboard-link" href="post-job.php">Post a New Job</a>
                    <a class="dashboard-link" href="applications.php">View Applications</a>

                    <h4>Your Job Listings</h4>
                    <div class="job-list">
                    <?php
                    $stmt = $conn->prepare("SELECT id, title FROM jobs WHERE provider_id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        echo "<ul>";
                        while ($job = $result->fetch_assoc()) {
                            echo "<li><a href='job-details.php?job_id=" . $job['id'] . "'>" . htmlspecialchars($job['title']) . "</a></li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>You haven't posted any jobs yet.</p>";
                    }
                    $stmt->close();
                    ?>
                    </div>
                </section>
            <?php } ?>

            <?php if ($user_role === 'admin') { ?>
                <section class="dashboard-card admin-section">
                    <h3>Administrator Tools</h3>
                    <a class="dashboard-link" href="../admin/admin-dashboard.php">Admin Dashboard</a>
                    <a class="dashboard-link" href="../admin/manage-users.php">Manage Users</a>
                    <a class="dashboard-link" href="../admin/manage-jobs.php">Manage Jobs</a>
                </section>
            <?php } ?>

            <section class="dashboard-card profile-section">
                <h3>Account Settings</h3>
                <a class="dashboard-link" href="profile.php">Edit Profile</a>
                <a class="dashboard-link logout-btn" href="../logout.php">Logout</a>
            </section>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

<script>
    const toggle = document.getElementById('themeSwitch');
    toggle.addEventListener('change', () => {
        document.body.classList.toggle('dark-mode');
    });
</script>
