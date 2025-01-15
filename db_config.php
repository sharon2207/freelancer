<?php
// Database connection settings
$host = 'localhost'; // Database host
$username = 'root';  // MySQL username (default for XAMPP/WAMP is 'root')
$password = '';      // MySQL password (leave blank for default setup)
$dbname = 'freelancer_platform'; // Name of the database you created

// Create a new MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // If connection fails, display an error message
}

// Uncomment the line below to confirm the connection
// echo "Connected successfully to the database!";
?>
