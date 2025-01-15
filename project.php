<?php
// Include the database connection
include('db_config.php');

// Check if 'id' exists in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $project_id = $_GET['id'];

    // Query to fetch the project from the database
    $query = "SELECT * FROM projects WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $project_id);  // 'i' means integer
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the project exists in the database
    if ($result->num_rows > 0) {
        // Fetch the project details
        $project = $result->fetch_assoc();
    } else {
        // If the project doesn't exist, show an error message
        echo "Project not found.";
        exit; // Stop further execution if no project found
    }
} else {
    // If no 'id' is passed, show an error message or redirect
    echo "Project ID is missing.";
    exit;
}

// Fetch tasks for this project
$task_query = "SELECT task_title, status FROM tasks WHERE project_id = ?";
$task_stmt = $conn->prepare($task_query);
$task_stmt->bind_param("i", $project_id);
$task_stmt->execute();
$task_result = $task_stmt->get_result();

// Fetch messages for this project
$message_query = "SELECT messages.message, users.name 
                  FROM messages 
                  INNER JOIN users ON messages.user_id = users.id 
                  WHERE messages.project_id = ?";
$message_stmt = $conn->prepare($message_query);
$message_stmt->bind_param("i", $project_id);
$message_stmt->execute();
$message_result = $message_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details</title>
    <link rel="stylesheet" href="style.css"> <!-- Assuming you have a style.css -->
</head>
<body>

<!-- Project Details Section -->
<div class="project-details">
    <?php
    // Check if the project array is valid and contains the 'title' key
    if (isset($project) && isset($project['title'])) {
        // Display the project details
        echo "<h1>" . htmlspecialchars($project['title']) . "</h1>";
        echo "<p>" . htmlspecialchars($project['description']) . "</p>";
    } else {
        echo "<p>Error: Project title is missing or project not found.</p>";
    }
    ?>
</div>

<!-- Tasks for this Project -->
<div class="tasks">
    <h2>Tasks for this Project</h2>
    <?php
    if ($task_result->num_rows > 0) {
        while ($task = $task_result->fetch_assoc()) {
            echo "<p>Task: " . htmlspecialchars($task['task_title']) . " - Status: " . htmlspecialchars($task['status']) . "</p>";
        }
    } else {
        echo "<p>No tasks found for this project.</p>";
    }
    ?>
</div>

<!-- Messages for this Project -->
<div class="messages">
    <h2>Messages for this Project</h2>
    <?php
    if ($message_result->num_rows > 0) {
        while ($message = $message_result->fetch_assoc()) {
            echo "<p>Message: " . htmlspecialchars($message['message']) . " - By: " . htmlspecialchars($message['name']) . "</p>";
        }
    } else {
        echo "<p>No messages found for this project.</p>";
    }
    ?>
</div>

<!-- Task Creation Form -->
<div class="create-task">
    <a href="create_task.php?project_id=<?php echo $project['id']; ?>">Create Task</a>
</div>

<!-- Send Message Form -->
<div class="send-message">
    <a href="send_message.php?project_id=<?php echo $project['id']; ?>">Send Message</a>
</div>

<!-- Navigation Section -->
<div class="navigation">
    <a href="dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>
