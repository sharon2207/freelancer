<?php
require 'db_config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view interests.");
}

$user_id = $_SESSION['user_id']; // Logged-in user

// Fetch tasks created by the logged-in user
$query = "
    SELECT 
        interests.id AS interest_id,
        tasks.task_title,
        users.name AS interested_user,
        users.email AS interested_user_email,
        interests.created_at AS interest_date
    FROM 
        interests
    JOIN tasks ON interests.task_id = tasks.id
    JOIN users ON interests.user_id = users.id
    JOIN projects ON tasks.project_id = projects.id
    WHERE 
        projects.user_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<h3>Users Interested in Your Projects:</h3>";
    while ($row = $result->fetch_assoc()) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
        echo "<p>Task: " . $row['task_title'] . "</p>";
        echo "<p>Interested User: " . $row['interested_user'] . " (" . $row['interested_user_email'] . ")</p>";
        echo "<p>Date: " . $row['interest_date'] . "</p>";
        echo "</div>";
    }
} else {
    echo "<p>No one has expressed interest in your projects yet.</p>";
}

$stmt->close();
$conn->close();
?>
