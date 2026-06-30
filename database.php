<?php

function getConnection(){
 $host     = 'localhost';
    $user     = 'root';
    $password = 'eunice';
    $database = 'livestock_db';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_errno) {
    error_log("DB connect error: " . $conn->connect_error);
    die("Database connection failed.");
}
// set charset
 return $conn;
}