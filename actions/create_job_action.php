<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR_Staff') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Insert job
        $sql = "INSERT INTO jobs (position, plantillaItemNo, description, department, salaryGrade, monthlySalary, deadline) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['position'],
            $_POST['plantillaItemNo'] ?? null,
            $_POST['description'] ?? null,
            $_POST['department'],
            $_POST['salaryGrade'] ?? null,
            $_POST['monthlySalary'] ?? null,
            $_POST['deadline']
        ]);
        
        // Get the newly created job ID
        $jobId = $pdo->lastInsertId();
        
        // Insert document requirements if any were selected
        if (isset($_POST['document_requirements']) && is_array($_POST['document_requirements'])) {
            $reqSql = "INSERT INTO job_requirements (jobId, documentTypeId) VALUES (?, ?)";
            $reqStmt = $pdo->prepare($reqSql);
            
            foreach ($_POST['document_requirements'] as $documentTypeId) {
                $reqStmt->execute([$jobId, $documentTypeId]);
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        header("Location: ../HR_staff/hr_dashboard.php?success=job_created");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        
        // For debugging, you might want to log the error
        error_log("Error creating job: " . $e->getMessage());
        
        // Redirect with error message
        header("Location: ../HR_staff/create_job.php?error=creation_failed");
        exit();
    }
} else {
    header("Location: ../HR_staff/create_job.php");
    exit();
}
?>