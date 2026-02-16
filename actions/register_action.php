<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hash_password = password_hash($password, PASSWORD_DEFAULT);

    $userRole = 'Applicant';

    $sql = "INSERT INTO users (firstName, lastName, email, password, userRole) VALUES (:firstName, :lastName, :email, :password, :userRole)";

    $stmt = $pdo->prepare($sql);

    try{

        $stmt->execute([
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':email' => $email,
            ':password' => $hash_password,
            ':userRole' => $userRole
        ]);

        header("Location: ../login.php?success=account_created");
        exit();
    }catch(PDOException $e){
        header("Location: ../register.php?error=account_creation_failed");
        exit();
    }
}