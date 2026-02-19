<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR_Staff') {
    header("Location: ../login.php");
    exit();
}

if (isset($_GET['id'])) {
    $applicationId = $_GET['id'];
    
    try {
        // Delete application files first (foreign key constraint)
        $stmt = $pdo->prepare("DELETE FROM application_files WHERE applicationId = ?");
        $stmt->execute([$applicationId]);
        
        // Delete the application
        $stmt = $pdo->prepare("DELETE FROM applications WHERE applicationId = ?");
        $stmt->execute([$applicationId]);
        
        header("Location: ../HR_staff/applications.php?success=deleted");
        exit();
        
    } catch (Exception $e) {
        error_log("Error deleting application: " . $e->getMessage());
        header("Location: ../HR_staff/applications.php?error=delete_failed");
        exit();
    }
} else {
    header("Location: ../HR_staff/applications.php");
    exit();
}
?>
