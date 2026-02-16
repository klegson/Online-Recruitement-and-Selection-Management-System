<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'HR_Staff') {
    header("Location: ../login.php");
    exit();
}
?>
<h1>Welcome to HR Staff Dashboard</h1>
<p><a href="../actions/logout.php">Logout</a></p>