<?php
session_start();
require 'db_config.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to express interest.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id']; // Get user ID from session
    $task_id = isset($_POST['task_id']) ? (int)$_POST['task_id'] : 0; // Sanitize input

    if ($task_id <= 0) {
        echo "Invalid task ID.";
        exit();
    }

    // Check if the task is marked as completed
    $check_task_query = "SELECT t.status 
                         FROM tasks t 
                         WHERE t.id = ?";
    $stmt = $conn->prepare($check_task_query);
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();

    // Check if task is completed
    if ($task && $task['status'] === 'Completed') {
        echo "You cannot express interest in this task as it is already completed.";
        exit();
    }

    // Check if interest already exists
    $check_query = "SELECT * FROM interests WHERE user_id = ? AND task_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $user_id, $task_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Insert interest
        $insert_query = "INSERT INTO interests (user_id, task_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ii", $user_id, $task_id);
        if ($stmt->execute()) {
            // Success, redirect or display success message
            echo "Interest expressed successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "You have already expressed interest in this task.";
    }
}
?>
