<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'freelancer_platform');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$project_id = $_GET['id']; // Get the project ID from URL

// Query to fetch the project details
$query = "SELECT * FROM projects WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $project_id);  // 'i' means integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $project = $result->fetch_assoc();
} else {
    echo "Project not found.";
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
</head>
<body>

<!-- Project Details Section -->
<div class="project-details">
    <h1><?php echo htmlspecialchars($project['title']); ?></h1>
    <p><?php echo htmlspecialchars($project['description']); ?></p>
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

<!-- Delete Project Form (Only for project owner) -->
<?php if ($_SESSION['user_id'] == $project['user_id']) { ?>
    <form action="delete_project.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?');">
        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
        <button type="submit" name="delete_project">Delete Project</button>
    </form>
<?php } ?>

<!-- Navigation Section -->
<div class="navigation">
    <a href="dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>

<?php $conn->close(); ?>
