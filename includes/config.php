<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Database configuration
$host = "localhost"; 
$dbname = "u328114756_cakeorder_db"; 
$username = "u328114756_cakeorder_user"; 
$password = "/01[dLE^2Nv";

// Connect to MySQL database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
