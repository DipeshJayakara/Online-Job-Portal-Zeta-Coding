<?php
function calculate_ats_score($resumePath) {
    // ✅ Check if resume file exists
    if (!file_exists($resumePath)) {
        return 0; // If resume file is missing, return 0
    }

    // ✅ Read resume content
    $resumeText = file_get_contents($resumePath);

    // ✅ Define sample keywords (Modify as needed)
    $keywords = ['PHP', 'JavaScript', 'MySQL', 'HTML', 'CSS', 'Python', 'Laravel'];

    // ✅ Calculate ATS Score
    $score = 0;
    foreach ($keywords as $keyword) {
        if (stripos($resumeText, $keyword) !== false) {
            $score += 10; // Add 10 points for each matched keyword
        }
    }

    // ✅ Normalize the score (Max 100)
    return min($score, 100);
}
?>
