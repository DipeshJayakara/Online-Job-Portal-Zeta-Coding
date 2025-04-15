<?php
require_once '../includes/auth.php';
require_once '../config/connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$userQuery = mysqli_prepare($conn, "SELECT name, email, resume, degree, university, passing_year, achievements, certificate, skills, additional_education FROM users WHERE id = ?");
mysqli_stmt_bind_param($userQuery, "i", $user_id);
mysqli_stmt_execute($userQuery);
$result = mysqli_stmt_get_result($userQuery);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($userQuery);

// Decode skills JSON for display
$skillsArray = [];
if (!empty($user['skills'])) {
    $skillsArray = json_decode($user['skills'], true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $degree = trim($_POST['degree']);
    $university = trim($_POST['university']);
    $passing_year = trim($_POST['passing_year']);
    $achievements = trim($_POST['achievements']);
    $additional_education = trim($_POST['additional_education']);

    // Convert posted skills from JSON string to array
    $skills = json_decode($_POST['skills'], true);
    if (!is_array($skills)) {
        $skills = [];
    }

    $skills_json = json_encode($skills); // now safe to store in JSON column

    // Resume Upload
    $resume_name = $user['resume'];
    if (!empty($_FILES['resume']['name'])) {
        $resume_dir = "../uploads/resumes/";
        $resume_name = time() . "_" . basename($_FILES["resume"]["name"]);
        $resume_path = $resume_dir . $resume_name;
        move_uploaded_file($_FILES["resume"]["tmp_name"], $resume_path);
    }

    // Certificate Upload
    $certificate_name = $user['certificate'];
    if (!empty($_FILES['certificate']['name'])) {
        $certificate_dir = "../uploads/certificates/";
        $certificate_name = time() . "_" . basename($_FILES["certificate"]["name"]);
        $certificate_path = $certificate_dir . $certificate_name;
        move_uploaded_file($_FILES["certificate"]["tmp_name"], $certificate_path);
    }

    $updateQuery = "UPDATE users SET name = ?, email = ?, resume = ?, degree = ?, university = ?, passing_year = ?, achievements = ?, certificate = ?, skills = ?, additional_education = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssssssssssi", $name, $email, $resume_name, $degree, $university, $passing_year, $achievements, $certificate_name, $skills_json, $additional_education, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='profile.php';</script>";
        exit();
    } else {
        die("Error updating profile: " . mysqli_error($conn));
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>
    <h2>Edit Profile</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Academic Qualification:</label>
        <input type="text" name="degree" value="<?= htmlspecialchars($user['degree'] ?? '') ?>">

        <label>University:</label>
        <input type="text" name="university" value="<?= htmlspecialchars($user['university'] ?? '') ?>">

        <label>Year of Passing:</label>
        <input type="number" name="passing_year" value="<?= htmlspecialchars($user['passing_year'] ?? '') ?>">

        <label>Achievements:</label>
        <textarea name="achievements"><?= htmlspecialchars($user['achievements'] ?? '') ?></textarea>

        <label>Skills:</label>
        <input type="text" id="skill-input" placeholder="Enter a skill">
        <button type="button" onclick="addSkill()">Add Skill</button>
        <ul id="skill-list"></ul>
        <input type="hidden" name="skills" id="skills-json">

        <label>Additional Education:</label>
        <textarea name="additional_education"><?= htmlspecialchars($user['additional_education'] ?? '') ?></textarea>

        <label>Upload Resume:</label>
        <input type="file" name="resume" accept=".pdf,.doc,.docx">

        <label>Upload Certificate:</label>
        <input type="file" name="certificate" accept=".pdf,.doc,.docx">

        <button type="submit">Update Profile</button>
    </form>

    <a href="dashboard.php">Back to Dashboard</a>

    <script>
        let skills = <?= json_encode($skillsArray) ?>;

        function addSkill() {
            let input = document.getElementById('skill-input');
            let skill = input.value.trim();
            if (skill && !skills.includes(skill)) {
                skills.push(skill);
                updateSkills();
                input.value = '';
            }
        }

        function removeSkill(skill) {
            skills = skills.filter(s => s !== skill);
            updateSkills();
        }

        function updateSkills() {
            const list = document.getElementById('skill-list');
            list.innerHTML = '';
            skills.forEach(skill => {
                let li = document.createElement('li');
                li.innerHTML = `${skill} <button type="button" onclick="removeSkill('${skill}')">Remove</button>`;
                list.appendChild(li);
            });
            document.getElementById('skills-json').value = JSON.stringify(skills);
        }

        updateSkills(); // Initialize list on page load
    </script>
</body>
</html>
