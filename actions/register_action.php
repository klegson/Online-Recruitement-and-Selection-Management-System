<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $extension = $_POST['extension'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hash_password = password_hash($password, PASSWORD_DEFAULT);

    $userRole = 'Applicant';

    $sql = "INSERT INTO users (firstName, middleName, lastName, extension, email, password, userRole) VALUES (:firstName, :middleName, :lastName, :extension, :email, :password, :userRole)";

    $stmt = $pdo->prepare($sql);

    try{

        $stmt->execute([
            ':firstName' => $firstName,
            ':middleName' => $middleName,
            ':lastName' => $lastName,
            ':extension' => $extension,
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