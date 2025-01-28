<?php
require 'db_config.php'; // Connect to the database
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You need to log in to access this page.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $task_id = $_POST['task_id'];

    // Check if the user has already expressed interest
    $query = "SELECT * FROM interests WHERE user_id = ? AND task_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $task_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "You have already expressed interest in this task.";
    } else {
        // Insert interest into the database
        $query = "INSERT INTO interests (user_id, task_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $task_id);
        if ($stmt->execute()) {
            echo "Interest expressed successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>
<a href="dashboard.php">Back to Dashboard</a>
