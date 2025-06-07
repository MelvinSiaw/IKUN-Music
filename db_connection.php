<?php
// Database configuration
$servername = "localhost"; // Replace with your MySQL hostname
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "ikun_music"; // Replace with your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

return $conn;
