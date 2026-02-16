<?php

$host = "127.0.0.1";
$user = "root";
$pass = "localpass";
$db = "deped_db";

$dsn = "mysql:host=$host;dbname=$db";

$opt = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
);

try{
    $pdo = new PDO($dsn, $user, $pass, $opt);
} catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
}
