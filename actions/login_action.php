<?php 
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        $_SESSION['error'] = "No account found with that email address.";
        header("Location: ../login.php");
        exit();
    }

    if (!password_verify($password, $user['password'])) {
        $_SESSION['error'] = "Incorrect password. Please try again.";
        header("Location: ../login.php");
        exit();
    }

    $_SESSION['user_id'] = $user['userId'];
    $_SESSION['user_role'] = $user['userRole'];

    if ($user['userRole'] === 'Admin') {
        header("Location: ../admin/admin_dashboard.php");
    } elseif ($user['userRole'] === 'HR_Staff') {
        header("Location: ../HR_staff/hr_dashboard.php");
    } elseif ($user['userRole'] === 'Applicant') {
        header("Location: ../Applicants/applicants_dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
} else {
    header("Location: ../login.php");
    exit();
}
?>