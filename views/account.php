<?php
session_start();
require_once '../config/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch User Details
$stmt = $conn->prepare("SELECT name, email, role, resume, degree, university, passing_year, achievements, certificate, skills, additional_education FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Convert JSON fields to arrays
$skills = json_decode($user['skills'] ?? '[]', true);
$additional_education = json_decode($user['additional_education'] ?? '[]', true);

// ✅ Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $degree = trim($_POST['degree']);
    $university = trim($_POST['university']);
    $passing_year = trim($_POST['passing_year']);
    $achievements = trim($_POST['achievements']);
    $skills = json_encode($_POST['skills'] ?? []);
    $additional_education = json_encode($_POST['additional_education'] ?? []);

    // ✅ Handle Resume Upload
    if (!empty($_FILES['resume']['name'])) {
        $resume_dir = "../uploads/resumes/";
        $resume_name = time() . "_" . basename($_FILES["resume"]["name"]);
        $resume_path = $resume_dir . $resume_name;
        
        if (move_uploaded_file($_FILES["resume"]["tmp_name"], $resume_path)) {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, resume = ?, degree = ?, university = ?, passing_year = ?, achievements = ?, skills = ?, additional_education = ? WHERE id = ?");
            $stmt->bind_param("sssssssssi", $name, $email, $resume_name, $degree, $university, $passing_year, $achievements, $skills, $additional_education, $user_id);
        }
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, degree = ?, university = ?, passing_year = ?, achievements = ?, skills = ?, additional_education = ? WHERE id = ?");
        $stmt->bind_param("ssssssssi", $name, $email, $degree, $university, $passing_year, $achievements, $skills, $additional_education, $user_id);
    }

    // ✅ Handle Certificate Upload
    if (!empty($_FILES['certificate']['name'])) {
        $certificate_dir = "../uploads/certificates/";
        $certificate_name = time() . "_" . basename($_FILES["certificate"]["name"]);
        $certificate_path = $certificate_dir . $certificate_name;
        
        if (move_uploaded_file($_FILES["certificate"]["tmp_name"], $certificate_path)) {
            $stmt_cert = $conn->prepare("UPDATE users SET certificate = ? WHERE id = ?");
            $stmt_cert->bind_param("si", $certificate_name, $user_id);
            $stmt_cert->execute();
            $stmt_cert->close();
        }
    }

    if ($stmt->execute()) {
        header("Location: account.php?success=updated");
        exit();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Account</title>
    <link rel="stylesheet" href="../assets/css/account.css">
</head>
<?php include '../includes/header.php'; ?>
<body>

<div class="account-container">
    
    <!-- Left Panel (User Info) -->
    <div class="left-panel">
        <h2>Profile Overview</h2>
        <div class="user-info">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
            <p><strong>Degree:</strong> <?= htmlspecialchars($user['degree'] ?? 'Not Added'); ?></p>
            <p><strong>University:</strong> <?= htmlspecialchars($user['university'] ?? 'Not Added'); ?></p>
            <p><strong>Year of Passing:</strong> <?= htmlspecialchars($user['passing_year'] ?? 'Not Added'); ?></p>
            <p><strong>Achievements:</strong> <?= htmlspecialchars($user['achievements'] ?? 'None'); ?></p>
            
            <p>Resume: 
                <?php if ($user['resume']) { ?>
                    <a href="../uploads/resumes/<?= $user['resume']; ?>" target="_blank" class="file-link">View Resume</a>
                <?php } else { echo "No resume uploaded"; } ?>
            </p>
            
            <p>Certificate: 
                <?php if ($user['certificate']) { ?>
                    <a href="../uploads/certificates/<?= $user['certificate']; ?>" target="_blank" class="file-link">View Certificate</a>
                <?php } else { echo "No certificate uploaded"; } ?>
            </p>
        </div>
    </div>

    <!-- Right Panel (Update Form) -->
    <div class="right-panel">
        <form action="account.php" method="POST" enctype="multipart/form-data">
            <label>Full Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

            <label>Academic Qualification:</label>
            <input type="text" name="degree" value="<?= htmlspecialchars($user['degree'] ?? ''); ?>" placeholder="e.g., B.Sc Computer Science">

            <label>University:</label>
            <input type="text" name="university" value="<?= htmlspecialchars($user['university'] ?? ''); ?>" placeholder="e.g., Harvard University">

            <label>Year of Passing:</label>
            <input type="number" name="passing_year" value="<?= htmlspecialchars($user['passing_year'] ?? ''); ?>" placeholder="e.g., 2023">

            <label>Achievements:</label>
            <textarea name="achievements"><?= htmlspecialchars($user['achievements'] ?? ''); ?></textarea>

            <label>Upload Resume:</label>
            <input type="file" name="resume" accept=".pdf,.doc,.docx">

            <label>Upload Certificate:</label>
            <input type="file" name="certificate" accept=".pdf,.doc,.docx">

            <button type="submit">Update Profile</button>
        </form>
    </div>

</div>

<a href="dashboard.php" class="back-btn">Back to Dashboard</a>

<?php include '../includes/footer.php'; ?>

<script>
    let skills = <?= $user['skills'] ? $user['skills'] : '[]' ?>;

    function addSkill() {
        let skillInput = document.getElementById('skill-input');
        let skill = skillInput.value.trim();
        if (skill && !skills.includes(skill)) {
            skills.push(skill);
            updateSkillList();
            skillInput.value = "";
        }
    }

    function removeSkill(skill) {
        skills = skills.filter(s => s !== skill);
        updateSkillList();
    }

    function updateSkillList() {
        let skillList = document.getElementById('skill-list');
        skillList.innerHTML = "";
        skills.forEach(skill => {
            let li = document.createElement('li');
            li.innerHTML = `${skill} <button type='button' onclick='removeSkill("${skill}")'>Remove</button>`;
            skillList.appendChild(li);
        });
        document.getElementById('skills-json').value = JSON.stringify(skills);
    }

    updateSkillList();
</script>

</body>
</html>
