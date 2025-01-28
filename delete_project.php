<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_project'])) {
    $project_id = $_POST['project_id'];

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'freelancer_platform');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Delete tasks associated with the project
    $delete_tasks_sql = "DELETE FROM tasks WHERE project_id = ?";
    $stmt = $conn->prepare($delete_tasks_sql);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();

    // Delete project
    $delete_project_sql = "DELETE FROM projects WHERE id = ?";
    $stmt = $conn->prepare($delete_project_sql);
    $stmt->bind_param("i", $project_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Project and related tasks deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting project.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the dashboard
    header('Location: dashboard.php');
    exit();
}
?>
