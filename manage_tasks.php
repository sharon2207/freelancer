<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$project_id = $_GET['id']; // Get project ID from URL
$conn = new mysqli('localhost', 'root', '', 'freelancer_platform');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch tasks related to the project
$sql = "SELECT * FROM tasks WHERE project_id='$project_id'";
$tasks_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tasks</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="project">
        <h2>Tasks for Project ID: <?= $project_id ?></h2>
        <ul>
            <?php while ($task = $tasks_result->fetch_assoc()): ?>
                <li>
                    <?= $task['task_title'] ?> - <?= $task['status'] ?>
                    <a href="edit_task.php?task_id=<?= $task['id'] ?>">Edit</a>
                </li>
            <?php endwhile; ?>
        </ul>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
