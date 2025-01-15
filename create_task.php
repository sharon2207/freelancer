<?php
session_start();

// Include database configuration
include('db_config.php');

// Check if the form is submitted
if (isset($_POST['create_task'])) {
    // Get the form data
    $task_title = $_POST['task_title']; // Task Title from form
    $status = $_POST['status']; // Task Status from form
    $project_id = $_POST['project_id']; // Project ID to which task belongs

    // Prepare the SQL query to insert the new task into the database
    $query = "INSERT INTO tasks (task_title, status, project_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $task_title, $status, $project_id);

    // Execute the query and check if successful
    if ($stmt->execute()) {
        // Redirect to the project details page after success
        header("Location: project.php?id=$project_id");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch all available projects from the database to populate the dropdown
$project_query = "SELECT * FROM projects";
$project_result = $conn->query($project_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task</title>
    <link rel="stylesheet" href="style.css"> <!-- Assuming you have a style.css -->
</head>
<body>
    <h1>Create a New Task</h1>

    <!-- Task Creation Form -->
    <form method="POST" action="create_task.php">
        <label for="task_title">Task Title:</label>
        <input type="text" id="task_title" name="task_title" required><br>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
        </select><br>

        <label for="project_id">Select Project:</label>
        <select id="project_id" name="project_id" required>
            <?php while ($project = $project_result->fetch_assoc()) { ?>
                <option value="<?php echo $project['id']; ?>">
                    <?php echo $project['title']; ?>
                </option>
            <?php } ?>
        </select><br>

        <button type="submit" name="create_task">Create Task</button>
    </form>

    <!-- Back to Dashboard Button -->
    <a href="dashboard.php" class="button">Back to Dashboard</a>
</body>
</html>
