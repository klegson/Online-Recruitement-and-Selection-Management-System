<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR_Staff') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $applicationId = $_POST['applicationId'];
    $status = $_POST['status'];
    $hrNotes = $_POST['hrNotes'] ?? null;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE applications 
            SET status = ?, hrNotes = ?, updatedBy = ? 
            WHERE applicationId = ?
        ");
        $stmt->execute([$status, $hrNotes, $_SESSION['user_id'], $applicationId]);
        
        header("Location: ../HR_staff/view_application.php?id=$applicationId&success=updated");
        exit();
        
    } catch (Exception $e) {
        error_log("Error updating application: " . $e->getMessage());
        header("Location: ../HR_staff/view_application.php?id=$applicationId&error=update_failed");
        exit();
    }
} else {
    header("Location: ../HR_staff/applications.php");
    exit();
}
?>
