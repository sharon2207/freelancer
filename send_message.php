<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo 'You must be logged in to send a message.';
    exit();
}

if (isset($_POST['message']) && isset($_POST['task_id'])) {
    $message = $_POST['message'];
    $task_id = $_POST['task_id'];
    $user_id = $_SESSION['user_id'];

    // Connect to database
    $conn = new mysqli('localhost', 'root', '', 'freelancer_platform');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert the message into the messages table
    $stmt = $conn->prepare("INSERT INTO messages (user_id, task_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $task_id, $message);

    if ($stmt->execute()) {
        echo "Message sent successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
