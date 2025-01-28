<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'freelancer_platform');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the projects created by the user
$projects_sql = "SELECT * FROM projects WHERE user_id = '$user_id'";
$projects_result = $conn->query($projects_sql);

// Check if form is submitted to update project or task status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_project_status'])) {
        // Update project status
        $project_id = $_POST['project_id'];
        $new_status = $_POST['status'];

        $update_sql = "UPDATE projects SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $new_status, $project_id);
        
        if ($stmt->execute()) {
            if ($new_status == 'Completed') {
                // Update all task statuses to 'Completed'
                $update_task_status = "UPDATE tasks SET status = 'Completed' WHERE project_id = ?";
                $task_stmt = $conn->prepare($update_task_status);
                $task_stmt->bind_param("i", $project_id);
                $task_stmt->execute();
            }
            header('Location: dashboard.php');
            exit();
        } else {
            echo "Error updating project status: " . $stmt->error;
        }
    } elseif (isset($_POST['update_task_status'])) {
        // Update task status
        $task_id = $_POST['task_id'];
        $new_task_status = $_POST['task_status'];

        $update_task_sql = "UPDATE tasks SET status = ? WHERE id = ?";
        $task_stmt = $conn->prepare($update_task_sql);
        $task_stmt->bind_param("si", $new_task_status, $task_id);

        if ($task_stmt->execute()) {
            header('Location: dashboard.php');
            exit();
        } else {
            echo "Error updating task status: " . $task_stmt->error;
        }
    } elseif (isset($_POST['delete_project'])) {
        // Delete project
        $project_id = $_POST['project_id'];

        $delete_project_sql = "DELETE FROM projects WHERE id = ?";
        $stmt = $conn->prepare($delete_project_sql);
        $stmt->bind_param("i", $project_id);

        if ($stmt->execute()) {
            // Optionally delete related tasks
            $delete_tasks_sql = "DELETE FROM tasks WHERE project_id = ?";
            $task_stmt = $conn->prepare($delete_tasks_sql);
            $task_stmt->bind_param("i", $project_id);
            $task_stmt->execute();

            header('Location: dashboard.php');
            exit();
        } else {
            echo "Error deleting project: " . $stmt->error;
        }
    } elseif (isset($_POST['delete_task'])) {
        // Delete task
        $task_id = $_POST['task_id'];

        $delete_task_sql = "DELETE FROM tasks WHERE id = ?";
        $stmt = $conn->prepare($delete_task_sql);
        $stmt->bind_param("i", $task_id);

        if ($stmt->execute()) {
            header('Location: dashboard.php');
            exit();
        } else {
            echo "Error deleting task: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="dashboard">
        <h2>Welcome to Your Dashboard</h2>

        <!-- Create Links -->
        <a href="create_project.php" class="btn">Create New Project</a>
        <a href="create_task.php" class="btn">Create New Task</a>
        <a href="work_on_projects.php" class="btn">Work on Projects</a>
        <a href="view_interests.php" class="btn">view interests</a>

        <!-- Display Projects -->
        <h3>Your Projects</h3>
        <ul>
            <?php while ($project = $projects_result->fetch_assoc()): ?>
                <li>
                    <h4><?= htmlspecialchars($project['title']) ?></h4>
                    <p><?= htmlspecialchars($project['description']) ?></p>
                    <p>Status: <?= htmlspecialchars($project['status']) ?></p>

                    <!-- Update Project Status Form -->
                    <form method="POST" action="dashboard.php">
                        <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                        <select name="status" required>
                            <option value="Not Started" <?= $project['status'] == 'Not Started' ? 'selected' : '' ?>>Not Started</option>
                            <option value="In Progress" <?= $project['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Completed" <?= $project['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                        <button type="submit" name="update_project_status">Update Status</button>
                    </form>

                    <!-- Delete Project Form -->
                    <form method="POST" action="dashboard.php" onsubmit="return confirm('Are you sure you want to delete this project? This will also delete associated tasks.')">
                        <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                        <button type="submit" name="delete_project" style="color: red;">Delete Project</button>
                    </form>

                    <!-- Display Tasks for Each Project -->
                    <h5>Tasks for this Project</h5>
                    <ul>
                        <?php
                        // Fetch tasks for the current project
                        $project_id = $project['id'];
                        $task_result = $conn->query("SELECT * FROM tasks WHERE project_id = '$project_id'");
                        while ($task = $task_result->fetch_assoc()):
                        ?>
                            <li>
                                <span><?= htmlspecialchars($task['task_title']) ?> (Status: <?= htmlspecialchars($task['status']) ?>)</span>
                                
                                <!-- Update Task Status Form -->
                                <form method="POST" action="dashboard.php" style="display: inline;">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    <select name="task_status" required>
                                        <option value="Not Started" <?= $task['status'] == 'Not Started' ? 'selected' : '' ?>>Not Started</option>
                                        <option value="In Progress" <?= $task['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                        <option value="Completed" <?= $task['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    </select>
                                    <button type="submit" name="update_task_status">Update Task</button>
                                </form>

                                <!-- Delete Task Form -->
                                <form method="POST" action="dashboard.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this task?')">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    <button type="submit" name="delete_task" style="color: red;">Delete Task</button>
                                </form>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</body>
</html>

<?php
$conn->close();
?>
