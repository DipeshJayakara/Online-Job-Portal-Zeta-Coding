<?php
include 'config/config.php';
include 'config/connection.php';
include 'includes/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF Token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch and sanitize filter values
$title = isset($_GET['title']) ? htmlspecialchars(trim($_GET['title'])) : '';
$location = isset($_GET['location']) ? htmlspecialchars(trim($_GET['location'])) : '';
$salary_min = isset($_GET['salary_min']) && $_GET['salary_min'] !== '' ? (int) $_GET['salary_min'] : null;
$salary_max = isset($_GET['salary_max']) && $_GET['salary_max'] !== '' ? (int) $_GET['salary_max'] : null;
$job_type = isset($_GET['job_type']) ? htmlspecialchars(trim($_GET['job_type'])) : '';
$ats_min = isset($_GET['ats_min']) && $_GET['ats_min'] !== '' ? (int) $_GET['ats_min'] : null;

// Prepare SQL query dynamically
$sql = "SELECT id, company_name, title, description, location, salary, job_type, created_at FROM jobs WHERE 1=1";
$params = [];
$types = '';

if (!empty($title)) {
    $sql .= " AND title LIKE ?";
    $params[] = "%$title%";
    $types .= 's';
}
if (!empty($location)) {
    $sql .= " AND location LIKE ?";
    $params[] = "%$location%";
    $types .= 's';
}
if ($salary_min !== null) {
    $sql .= " AND salary >= ?";
    $params[] = $salary_min;
    $types .= 'i';
}
if ($salary_max !== null) {
    $sql .= " AND salary <= ?";
    $params[] = $salary_max;
    $types .= 'i';
}
if (!empty($job_type)) {
    $sql .= " AND job_type = ?";
    $params[] = $job_type;
    $types .= 's';
}
if ($ats_min !== null) {
    $sql .= " AND ats_min_score >= ?";
    $params[] = $ats_min;
    $types .= 'i';
}

$sql .= " ORDER BY created_at DESC LIMIT 10";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<head>
    <link rel="stylesheet" href="assets/css/style.css?v=3">
</head>
<main>
    <section class="hero">
        <div class="hero-content">
            <h1>Find Your Dream Job</h1>
            <p>Search from thousands of job listings and get hired today.</p>
            <a href="views/job-listings.php" class="btn">Browse Jobs</a>
        </div>
    </section>

    <div class="container">
        <aside class="filter-sidebar">
            <h3>Filter Jobs</h3>
            <form method="GET" action="index.php">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                <label for="title">Job Title:</label>
                <input type="text" name="title" id="title" value="<?= $title; ?>">

                <label for="location">Location:</label>
                <input type="text" name="location" id="location" value="<?= $location; ?>">

                <label for="salary_min">Min Salary:</label>
                <input type="number" name="salary_min" id="salary_min" value="<?= $salary_min ?? ''; ?>">

                <label for="salary_max">Max Salary:</label>
                <input type="number" name="salary_max" id="salary_max" value="<?= $salary_max ?? ''; ?>">

                <label for="job_type">Job Type:</label>
                <select name="job_type" id="job_type">
                    <option value="">Any</option>
                    <option value="Full-Time" <?= ($job_type == 'Full-Time') ? 'selected' : ''; ?>>Full-Time</option>
                    <option value="Part-Time" <?= ($job_type == 'Part-Time') ? 'selected' : ''; ?>>Part-Time</option>
                    <option value="Internship" <?= ($job_type == 'Internship') ? 'selected' : ''; ?>>Internship</option>
                </select>

                <label for="ats_min">Min ATS Score:</label>
                <input type="number" name="ats_min" id="ats_min" min="0" max="100" value="<?= $ats_min ?? ''; ?>">

                <button type="submit" class="filter-btn">Apply Filters</button>
            </form>
        </aside>

        <section class="job-listings">
            <h2>Latest Job Listings</h2>
            <?php if ($result->num_rows > 0) { ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <div class='job-card'>
                        <h3><?= htmlspecialchars($row['title']); ?></h3>
                        <p><strong>Company:</strong> <?= htmlspecialchars($row['company_name']); ?></p>
                        <p><?= htmlspecialchars($row['description']); ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($row['location']); ?></p>
                        <p><strong>Salary:</strong> $<?= htmlspecialchars($row['salary']); ?></p>
                        <p><strong>Type:</strong> <?= htmlspecialchars($row['job_type']); ?></p>
                        <p><strong>Posted:</strong> <?= date('M d, Y', strtotime($row['created_at'])); ?></p>
                        <a href='views/job-details.php?job_id=<?= $row['id']; ?>' class='details-btn'>View Details</a>
                        <a href='apply.php?job_id=<?= $row['id']; ?>' class='apply-btn'>Apply Now</a>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>No job listings available.</p>
            <?php } ?>
        </section>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
