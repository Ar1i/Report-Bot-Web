<?php
include('config.php');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("MySQL Connection Error: " . $conn->connect_error);
} 
?>