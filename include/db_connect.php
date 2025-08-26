<?php
// Database credentials
$host = "localhost";          // Usually localhost for XAMPP
$username = "root";           // Default XAMPP MySQL username
$password = "";               // Default XAMPP MySQL password is empty
$database = "sports-club";    // Your database name

// Create connection
$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Optional: set charset to avoid encoding issues
mysqli_set_charset($con, "utf8mb4");
?>
