<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit();
}

if (!isset($_GET['applicant_id']) || empty($_GET['applicant_id'])) {
    echo "<script>alert('Applicant ID is required'); window.location.href='manage-jobs.php';</script>";
    exit();
}

$applicant_id = $_GET['applicant_id'];

$query = "SELECT u.id AS user_id, u.name, u.email, u.phone, u.address, u.education, 
                 a.resume, a.status, j.title AS job_title, j.id AS job_id
          FROM applications a 
          LEFT JOIN users u ON a.user_id = u.id 
          LEFT JOIN jobs j ON a.job_id = j.id
          WHERE a.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$applicant_result = $stmt->get_result();
$applicant = $applicant_result->fetch_assoc();
$stmt->close();

if (!$applicant) {
    echo "<script>alert('Applicant not found'); window.location.href='manage-jobs.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicant Details</title>
    <link rel="stylesheet" href="../assets/css/admin-style.css">
    <style>
        .applicant-details {
            border: 1px solid #ccc;
            padding: 20px;
            margin: 20px auto;
            width: 70%;
            background: #f9f9f9;
            border-radius: 8px;
        }
        h3 {
            margin-top: 0;
        }
        .resume-link {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .resume-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Applicant Details</h2>
        
        <div class="applicant-details">
            <h3><?php echo htmlspecialchars($applicant['name']); ?></h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($applicant['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($applicant['phone']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($applicant['address']); ?></p>
            <p><strong>Education:</strong> <?php echo nl2br(htmlspecialchars($applicant['education'])); ?></p>
            <p><strong>Job Applied For:</strong> <?php echo htmlspecialchars($applicant['job_title']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($applicant['status']); ?></p>
            <h3>Resume</h3>
            <a class="resume-link" href="../uploads/resumes/<?php echo htmlspecialchars($applicant['resume']); ?>" target="_blank">📄 View Resume</a>
        </div>

        <br><br>
        <a href="job-details.php?job_id=<?php echo $applicant['job_id']; ?>">⬅ Back to Job Details</a>
    </div>
</body>
</html>
