<?php
$servername = "localhost";
$username = "root";  // Ensure this matches your phpMyAdmin user
$password = "";      // Ensure this matches your phpMyAdmin password
$dbname = "school_db"; // Name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
