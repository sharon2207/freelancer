<?php
// Assume $tasks contains a list of tasks retrieved from the database
foreach ($tasks as $task) {
    echo "<div>";
    echo "<h3>" . $task['task_title'] . "</h3>";
    echo "<p>Status: " . $task['status'] . "</p>";
    echo "<form method='POST' action='express_interest.php'>";
    echo "<input type='hidden' name='task_id' value='" . $task['id'] . "'>";
    echo "<button type='submit'>Express Interest</button>";
    echo "</form>";
    echo "</div>";
}
?>
