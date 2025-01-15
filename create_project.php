<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    // Connect to database
    $conn = new mysqli('localhost', 'root', '', 'freelancer_platform');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert project data into 'projects' table
    $sql = "INSERT INTO projects (title, description, user_id) VALUES ('$title', '$description', '$user_id')";
    if ($conn->query($sql) === TRUE) {
        echo "Project created successfully! <a href='dashboard.php'>Go to Dashboard</a>";
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
    <title>Create Project</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>Create New Project</h2>
        <form method="POST">
            <label>Title</label>
            <input type="text" name="title" required>
            <label>Description</label>
            <textarea name="description" required></textarea>
            <button type="submit">Create Project</button>
        </form>
    </div>
</body>
</html>
