<?php

include_once 'config/db.php';

if($pdo){
    echo "Connected successfully";
}else{
    echo "Connection failed";
}  