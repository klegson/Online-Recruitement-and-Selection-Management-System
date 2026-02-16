<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Applicant') {
    header("Location: ../login.php");
    exit();
}
?>
<h1>Welcome to Applicant Dashboard</h1>
<p><a href="../actions/logout.php">Logout</a></p>