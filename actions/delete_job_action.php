<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR_Staff') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: ../HR_staff/hr_dashboard.php");
    exit();
}

$jobId = $_GET['id'];

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Delete related applications and files first
    $stmt = $pdo->prepare("
        SELECT af.filePath 
        FROM application_files af 
        JOIN applications a ON af.applicationId = a.applicationId 
        WHERE a.jobId = ?
    ");
    $stmt->execute([$jobId]);
    $files = $stmt->fetchAll();
    
    // Delete physical files
    foreach ($files as $file) {
        if (file_exists($file['filePath'])) {
            unlink($file['filePath']);
        }
    }
    
    // Delete application files
    $stmt = $pdo->prepare("
        DELETE af FROM application_files af 
        JOIN applications a ON af.applicationId = a.applicationId 
        WHERE a.jobId = ?
    ");
    $stmt->execute([$jobId]);
    
    // Delete applications
    $stmt = $pdo->prepare("DELETE FROM applications WHERE jobId = ?");
    $stmt->execute([$jobId]);
    
    // Delete the job
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE jobId = ?");
    $stmt->execute([$jobId]);
    
    // Commit transaction
    $pdo->commit();
    
    $_SESSION['success'] = "Job posting deleted successfully!";
    
} catch (PDOException $e) {
    $pdo->rollback();
    $_SESSION['error'] = "Error deleting job: " . $e->getMessage();
}

header("Location: ../HR_staff/hr_dashboard.php");
exit();
?>
