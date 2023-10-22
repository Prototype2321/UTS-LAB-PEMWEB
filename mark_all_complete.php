<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["markAllComplete"])) {
    // Handle marking all tasks as complete here
    session_start();

    $db = new mysqli("localhost", "root", "", "task_tracker");

    // Get the user ID from the session
    $user_id = $_SESSION["user_id"];

    // Mark all tasks as complete for the user
    $sql = "UPDATE tasks SET is_completed = 1 WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: to-do.php");
        exit;
    } else {
        echo "Error marking tasks as completed: " . $stmt->error;
    }

    $stmt->close();
}
?>
