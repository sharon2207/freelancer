<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$task_id = $_GET['task_id']; // Get task ID from URL

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];

    // Connect to database
    $conn = new mysqli('localhost', 'root', '', 'freelancer_platform');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update task status
    $sql = "UPDATE tasks SET status='$status' WHERE id='$task_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Task updated successfully! <a href='manage_tasks.php?id=$task_id'>Back to Task List</a>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>Edit Task Status</h2>
        <form method="POST">
            <label>Status</label>
            <select name="status">
                <option value="Incomplete">Incomplete</option>
                <option value="Complete">Complete</option>
            </select>
            <button type="submit">Update Status</button>
        </form>
    </div>
</body>
</html>
