<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR_Staff') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $jobId = $_GET['id'];
    
    // Get current status
    $stmt = $pdo->prepare("SELECT jobStatus FROM jobs WHERE jobId = ?");
    $stmt->execute([$jobId]);
    $job = $stmt->fetch();
    
    if ($job) {
        // Toggle status
        $newStatus = ($job['jobStatus'] === 'Open') ? 'Closed' : 'Open';
        
        // If reopening, update postedAt to current timestamp
        if ($newStatus === 'Open') {
            $stmt = $pdo->prepare("UPDATE jobs SET jobStatus = ?, postedAt = CURRENT_TIMESTAMP WHERE jobId = ?");
        } else {
            $stmt = $pdo->prepare("UPDATE jobs SET jobStatus = ? WHERE jobId = ?");
        }
        $stmt->execute([$newStatus, $jobId]);
    }
}

header("Location: ../HR_staff/hr_dashboard.php");
exit();
?>
