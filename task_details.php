<?php
require 'db_connection.php';

// Get the task ID
$task_id = $_GET['task_id'];

// Fetch task details
$task_query = "SELECT * FROM tasks WHERE id = ?";
$stmt = $conn->prepare($task_query);
$stmt->bind_param("i", $task_id);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();

// Fetch users who expressed interest
$interest_query = "SELECT users.name, users.email FROM interests 
                   JOIN users ON interests.user_id = users.id 
                   WHERE interests.task_id = ?";
$stmt = $conn->prepare($interest_query);
$stmt->bind_param("i", $task_id);
$stmt->execute();
$interested_users = $stmt->get_result();

echo "<h3>" . $task['task_title'] . "</h3>";
echo "<p>Status: " . $task['status'] . "</p>";
echo "<h4>Interested Users:</h4>";
while ($user = $interested_users->fetch_assoc()) {
    echo "<p>" . $user['name'] . " (" . $user['email'] . ")</p>";
}
?>
