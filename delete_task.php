<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <title>Delete Task</title>
</head>

<style>
    body {
        background-color: #f0f0f0;
        font-family: 'Arial', sans-serif;
        background-image: url('Asset/todolistbg.jpg');
        background-size: cover;
    }

    .bg-white {
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.9);
        border-radius: 20px;
        backdrop-filter: blur(100px);
    }

    .flex-center {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
</style>
<body class="bg-gray-100 flex-center">

<?php
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: to-do.php");
    exit;
}

if (isset($_GET["id"])) {
    $task_id = $_GET["id"];
    $db = new mysqli("localhost", "root", "", "task_tracker");

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    $email = $_SESSION["email"];
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirmed"])) {
        // The task deletion is confirmed
        $sql = "DELETE FROM tasks WHERE id = ? AND user_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ii", $task_id, $user_id);

        if ($stmt->execute()) {
            $_SESSION['delete_success'] = true;
            header("Location: to-do.php");
            exit;
        } else {
            echo "Error deleting task: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // Display a confirmation dialog
        echo '<div class="p-4 bg-white rounded-lg shadow-md">';
        echo '<p>Are you sure you want to delete this task?</p>';
        echo '<form method="post">';
        echo '<div class="flex justify-center">';
        echo '<input type="submit" name="confirmed" value="Yes" class="bg-red-500 text-white px-2 py-2 ml-1 rounded hover:bg-red-600">';
        echo '<a href="to-do.php" class="bg-blue-500 rounded hover:bg-blue-600 text-white px-2 py-2 ml-1">No</a>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    }
} else {
    echo "Task ID is not provided.";
}
?>

</body>
</html>
