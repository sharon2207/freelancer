<?php
// Include database connection
require 'db_config.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'])) {
    $project_id = intval($_POST['project_id']); // Get the project ID

    // Step 1: Check if all tasks in the project are marked as "Complete"
    $checkTasksQuery = "SELECT COUNT(*) AS incomplete_tasks FROM tasks WHERE project_id = ? AND status != 'Complete'";
    $stmt = $conn->prepare($checkTasksQuery);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    if ($data['incomplete_tasks'] > 0) {
        // Redirect back with an error if not all tasks are complete
        header("Location: ../dashboard.php?error=Incomplete tasks remaining.");
        exit;
    }

    // Step 2: Delete all tasks related to the project
    $deleteTasksQuery = "DELETE FROM tasks WHERE project_id = ?";
    $stmt = $conn->prepare($deleteTasksQuery);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();

    // Step 3: Delete all interests related to the project's tasks
    $deleteInterestsQuery = "DELETE FROM interests WHERE task_id IN (SELECT id FROM tasks WHERE project_id = ?)";
    $stmt = $conn->prepare($deleteInterestsQuery);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();

    // Step 4: Optionally delete the project itself
    $deleteProjectQuery = "DELETE FROM projects WHERE id = ?";
    $stmt = $conn->prepare($deleteProjectQuery);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();

    // Step 5: Redirect back with a success message
    header("Location: ../dashboard.php?success=Project marked as complete.");
    exit;
}

// If the request is invalid, redirect back
header("Location: ../dashboard.php?error=Invalid request.");
exit;
?>
