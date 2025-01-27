<?php
require 'db_config.php'; // Connect to the database
session_start();

if (!isset($_SESSION['user_id'])) {
    die("You need to log in to access this page.");
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Fetch all projects except those created by the logged-in user
$query = "SELECT p.id AS project_id, p.title, p.description, u.name AS creator_name 
          FROM projects p 
          JOIN users u ON p.user_id = u.id 
          WHERE p.user_id != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$projects = $stmt->get_result();

echo "<h3>Work on Projects</h3>";

if ($projects->num_rows > 0) {
    while ($project = $projects->fetch_assoc()) {
        echo "<div>";
        echo "<h4>" . htmlspecialchars($project['title']) . "</h4>";
        echo "<p>Description: " . htmlspecialchars($project['description']) . "</p>";
        echo "<p>Posted by: " . htmlspecialchars($project['creator_name']) . "</p>";
        echo "<a href='view_project.php?project_id=" . $project['project_id'] . "'>View Project Details</a>";
        echo "</div><hr>";
    }
} else {
    echo "<p>No projects available to work on!</p>";
}

?>
<a href="dashboard.php">Back to Dashboard</a>
