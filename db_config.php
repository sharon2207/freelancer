<?php
// Database connection settings
$host = 'localhost';            // Database host (default for XAMPP/WAMP is 'localhost')
$username = 'root';             // MySQL username (default for XAMPP/WAMP is 'root')
$password = '';                 // MySQL password (leave blank for default setup)
$dbname = 'freelancer_platform'; // Name of your database (replace with your actual database name)

try {
    // Create a new MySQLi connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check for connection errors
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Uncomment the line below for debugging or to confirm a successful connection
    // echo "Connected successfully to the database!";
} catch (Exception $e) {
    // Handle connection errors gracefully
    die("Database Connection Error: " . $e->getMessage());
}
?>
