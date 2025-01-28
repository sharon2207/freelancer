<?php
// Include the database connection
include('db_config.php');

// Check if 'id' and 'project_id' exist in the URL
if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['project_id']) && is_numeric($_GET['project_id'])) {
    $task_id = $_GET['id'];
    $project_id = $_GET['project_id'];

    // Prepare the delete query to remove the task from the database
    $delete_query = "DELETE FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $task_id);  // 'i' means integer

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to the project details page after successful deletion
        header("Location: project.php?id=" . $project_id);  // Pass project_id in URL to go back to the project
        exit;
    } else {
        // If deletion fails, show an error message
        echo "Error deleting task.";
    }
} else {
    // If 'id' or 'project_id' is not set or is invalid, redirect to the project details page
    echo "Invalid task ID.";
}
?>
