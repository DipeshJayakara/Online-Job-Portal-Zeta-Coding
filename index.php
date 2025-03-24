<?php
include 'config/config.php';
include 'config/connection.php';
include 'includes/header.php';

// Fetch filter values from GET request
$title = isset($_GET['title']) ? $_GET['title'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$salary_min = isset($_GET['salary_min']) ? $_GET['salary_min'] : '';
$salary_max = isset($_GET['salary_max']) ? $_GET['salary_max'] : '';
$job_type = isset($_GET['job_type']) ? $_GET['job_type'] : '';

// Build SQL query with filters
$sql = "SELECT job_id, title, description, location, salary, job_type FROM jobs WHERE 1";

if (!empty($title)) {
    $sql .= " AND title LIKE '%$title%'";
}
if (!empty($location)) {
    $sql .= " AND location LIKE '%$location%'";
}
if (!empty($salary_min)) {
    $sql .= " AND salary >= $salary_min";
}
if (!empty($salary_max)) {
    $sql .= " AND salary <= $salary_max";
}
if (!empty($job_type)) {
    $sql .= " AND job_type = '$job_type'";
}

$sql .= " ORDER BY posted_at DESC LIMIT 5";
$result = $conn->query($sql);
?>
<head>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Find Your Dream Job</h1>
            <p>Search from thousands of job listings and get hired today.</p>
            <a href="views/job-listings.php" class="btn">Browse Jobs</a>
        </div>
    </section>

    <div class="container">
        <!-- Sidebar Filters -->
        <aside class="filter-sidebar">
            <h3>Filter Jobs</h3>
            <form method="GET" action="index.php">
                <label for="title">Job Title:</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($title); ?>">

                <label for="location">Location:</label>
                <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($location); ?>">

                <label for="salary_min">Min Salary:</label>
                <input type="number" name="salary_min" id="salary_min" value="<?php echo htmlspecialchars($salary_min); ?>">

                <label for="salary_max">Max Salary:</label>
                <input type="number" name="salary_max" id="salary_max" value="<?php echo htmlspecialchars($salary_max); ?>">

                <label for="job_type">Job Type:</label>
                <select name="job_type" id="job_type">
                    <option value="">Any</option>
                    <option value="Full-Time" <?php echo ($job_type == 'Full-Time') ? 'selected' : ''; ?>>Full-Time</option>
                    <option value="Part-Time" <?php echo ($job_type == 'Part-Time') ? 'selected' : ''; ?>>Part-Time</option>
                    <option value="Internship" <?php echo ($job_type == 'Internship') ? 'selected' : ''; ?>>Internship</option>
                </select>

                <button type="submit" class="filter-btn">Apply Filters</button>
            </form>
        </aside>

        <!-- Job Listings Section -->
        <section class="job-listings">
            <h2>Latest Job Listings</h2>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='job-card'>
                            <h3>" . htmlspecialchars($row['title']) . "</h3>
                            <p>" . htmlspecialchars($row['description']) . "</p>
                            <p><strong>Location:</strong> " . htmlspecialchars($row['location']) . "</p>
                            <p><strong>Salary:</strong> $" . htmlspecialchars($row['salary']) . "</p>
                            <p><strong>Type:</strong> " . htmlspecialchars($row['job_type']) . "</p>
                            <a href='views/job-details.php?job_id=" . $row['job_id'] . "' class='details-btn'>View Details</a>
                            <a href='apply.php?job_id=" . $row['job_id'] . "' class='apply-btn'>Apply Now</a>
                          </div>";
                }
            } else {
                echo "<p>No job listings available.</p>";
            }
            ?>
        </section>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
