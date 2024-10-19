<?php
$servername = "localhost"; // Replace with your server name
$username = ""; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "water_billing_system"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
