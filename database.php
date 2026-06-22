<?php
// config/db.php
// Simple mysqli connection wrapper. Edit credentials as needed.
function getConnection(){
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '8888';
$DB_NAME = 'lhvts_db';
$DB_PORT = 3307; // change to 3306 if needed

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
if ($conn->connect_errno) {
    error_log("DB connect error: " . $conn->connect_error);
    die("Database connection failed.");
}
// set charset
 return $conn;
}