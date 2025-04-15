<?php
require_once '../config/connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['application_ids'];
    $statuses = $_POST['statuses'];

    for ($i = 0; $i < count($ids); $i++) {
        $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
        if ($stmt === false) {
            error_log('SQL error: ' . $conn->error);
            exit('Database prepare error');
        }
        
        $stmt->bind_param("si", $statuses[$i], $ids[$i]);
        
        if (!$stmt->execute()) {
            error_log('SQL execution error: ' . $stmt->error);
        }
    }

    // ✅ Correct redirect
    header("Location: applications.php?updated=true");
    exit();
}
?>
