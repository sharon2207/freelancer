<?php
require 'db_config.php'; // Connect to the database
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You need to log in to access this page.");
}

$project_id = $_GET['project_id'];

// Fetch project details
$query = "SELECT p.title, p.description, u.name AS creator_name 
          FROM projects p 
          JOIN users u ON p.user_id = u.id 
          WHERE p.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();

if (!$project) {
    die("Project not found.");
}

echo "<h3>Project: " . htmlspecialchars($project['title']) . "</h3>";
echo "<p>Description: " . htmlspecialchars($project['description']) . "</p>";
echo "<p>Posted by: " . htmlspecialchars($project['creator_name']) . "</p>";

// Fetch tasks related to the project
$query = "SELECT id, task_title, status FROM tasks WHERE project_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$tasks = $stmt->get_result();

if ($tasks->num_rows > 0) {
    echo "<h4>Tasks:</h4>";
    while ($task = $tasks->fetch_assoc()) {
        echo "<div>";
        echo "<h5>" . htmlspecialchars($task['task_title']) . "</h5>";
        echo "<p>Status: " . htmlspecialchars($task['status']) . "</p>";
        echo "<form method='POST' action='express_interest.php'>";
        echo "<input type='hidden' name='task_id' value='" . $task['id'] . "'>";
        echo "<button type='submit'>Express Interest</button>";
        echo "</form>";
        echo "</div><hr>";
    }
} else {
    echo "<p>No tasks available for this project.</p>";
}

?>
<a href="work_on_projects.php">Back to Projects</a>
