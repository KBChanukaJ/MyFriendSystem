<?php

$servername = "localhost"; // Assuming the database is on the same server as your PHP script
$username = "root";
$password = ""; // Change this to your password
$database = "friendappdb"; // Change this to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>
