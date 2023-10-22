<!DOCTYPE html>
<html>
<head>
<title>Task Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="">
    
</head>
<style>

body {background-color: #f0f0f0; /* Change the background color to your preference */
    background-image: url('Asset/todolistbg.jpg');
      background-size: cover;
  font-family: 'Arial', sans-serif; /* Change the font to your preference */
  backdrop-filter:5px;

 }
 .container {
            background-image: url('Asset/background.jpg'); /* Set the path to your image */
            background-size: cover; /* Adjust the image size to cover the container */
            background-position: center; /* Center the background image */
            border:2px solid rgba(255,255,255,0.9);
        }
  
 </style>
<body class="bg-gray-100 container-fluid">
    <div class="container mx-auto mt-10 p-4 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Task Tracker</h2>
        <!-- <p class="mt-4">Total Tasks Available: <?php echo $totalTasksCount; ?></p> -->

        <div class="flex flex-col items-start sm:flex-row sm:justify-between">
            <form method="post" action="add_task.php">
                <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 mb-2 sm:mb-0">Add Task</button>
            </form>
            
            <form method="post">
            <label for="sort">Sort by:</label>
            <select name="sort" id="sort">
                <option value="all">All</option>
                <option value="completed">Completed</option>
                <option value="not_completed">Not Completed</option>
            </select>
            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 mb-2 sm:mb-0">Sort</button>
        </form>

            <form method="post" action="mark_all_complete.php">
                <input type="checkbox" name="markAllComplete" id="markAllComplete">
                <label for="markAllComplete">Mark All as Completed</label>
                <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 mb-2 sm:mb-0">Submit</button>
            </form>
            
            <form method="post" action="logout.php">
                <button type="submit" class="bg-red-500 text-white font-bold py-2 px-4 rounded hover:bg-red-600 ml-0 sm:ml-4">Logout</button>
            </form>
        </div>
        <ul class="mt-4 grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4">
            
    <?php
    session_start();
    $db = new mysqli("localhost", "root", "", "task_tracker");
    // Fetch and display tasks for the logged-in user
    $email = $_SESSION["email"]; // Get the user's email from the session
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    $_SESSION["user_id"] = $user_id;

    // Query untuk mengambil tugas yang sudah selesai
$sql_completed = "SELECT id, title, description, progress, due_date, is_completed FROM tasks WHERE user_id = ? AND is_completed = 1";

// Query untuk mengambil tugas yang belum selesai
$sql_not_completed = "SELECT id, title, description, progress, due_date, is_completed FROM tasks WHERE user_id = ? AND is_completed = 0";

// Gabungkan hasil dari kedua query di atas
$sql = $sql_completed . " UNION " . $sql_not_completed;

// Sort hasil query sesuai kebutuhan (mungkin berdasarkan tanggal, jika diperlukan)
$sql .= " ORDER BY due_date ASC";

$stmt = $db->prepare($sql);
$stmt->bind_param("ii", $task ,$user_id);
$stmt->execute();
$result = $stmt->get_result();

    
    $sql = "SELECT COUNT(id) AS taskCount FROM tasks WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalTasksCount = $row['taskCount'];
    } else {
        $totalTasksCount = 0;
    }

    $sql = "SELECT id, title, description, progress,due_date ,is_completed FROM tasks WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $sort = isset($_POST["sort"]) ? $_POST["sort"] : "all"; // Default to "all" if not set

    if ($sort === "completed") {
        $sql = "SELECT id, title, description, progress, due_date, is_completed FROM tasks WHERE user_id = ? ORDER BY is_completed DESC, due_date ASC";
    } elseif ($sort === "not_completed") {
        $sql = "SELECT id, title, description, progress, due_date, is_completed FROM tasks WHERE user_id = ? ORDER BY is_completed DESC, due_date ASC";
    } else {
        $sql = "SELECT id, title, description, progress, due_date, is_completed FROM tasks WHERE user_id = ? ORDER BY due_date DESC";
    }

if (isset($_SESSION['delete_success']) && $_SESSION['delete_success']) {
    echo '<div class="bg-green-300 text-green-800 py-2 px-4 mb-2 rounded-md">Task deleted successfully!</div>';
    unset($_SESSION['delete_success']); // Remove the success flag from the session
}
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

    
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="bg-white border rounded-lg p-4 shadow-md">';
            echo "<strong class='text-xl mb-2'>" . htmlspecialchars($row["title"]) . "</strong><br>";
            echo "Description: " . htmlspecialchars($row["description"]) . "<br>";
            echo "Due Date: " .htmlspecialchars($row["due_date"]). "<br>";
            echo '<div id="status_text_' . $row["id"] . '">Status: ' . ($row["is_completed"] ? 'Completed' : 'Not Completed') . '</div>';
            echo '<label class="checkbox-container">Mark as Completed';
            echo '<input type="checkbox" id="status_checkbox_' . $row["id"] . '" onclick="updateStatus(' . $row["id"] . ')" ' . ($row["is_completed"] ? 'checked' : '') . '>';
            echo '<span class="checkmark"></span>';
            echo '</label>';
            echo '<br>';
            // Dropdown Progres untuk Tugas Ini
            echo "Progress: " . ($row["progress"]);
            echo '<form method="post" action="progress.php">';
            echo '<input type="hidden" name="task_id" value="' . $row["id"] . '">';
            echo '<select name="task_progress_' . $row["id"] . '" onchange="updateProgress(' . $row["id"] . ')">';
            echo '<option value="Not yet started" ' . ($row["progress"] == "Not yet started" ? 'selected' : '') . '>Not yet started</option>';
            echo '<option value="In progress" ' . ($row["progress"] == "In progress" ? 'selected' : '') . '>In progress</option>';
            echo '<option value="Waiting on" ' . ($row["progress"] == "Waiting on" ? 'selected' : '') . '>Waiting on</option>';
            // Tambahkan opsi progres lainnya sesuai kebutuhan
            echo '</select>';
            echo '<button type="submit" name="update_progress" class="bg-green-500 text-white px-2 py-2 ml-1 rounded hover:bg-green-600">Save</button>';
            echo '</form>';
        
          
            echo '<div class="mt-4">';
            echo '<a href="edit_task.php?id=' . $row["id"] . '" class="bg-blue-500   rounded hover:bg-blue-600 text-white px-2 py-2 ml-0">Edit</a>';
            echo '<a href="delete_task.php?id=' . $row["id"] . '" class="bg-red-500 rounded hover:bg-red-600 text-white px-2  py-2 ml-1">Delete</a>';
             //echo '<a href="progress.php?id=' . $row["id"] . '" class="text-green-500 hover:underline ml-2">Save</a>';
            echo '</div>';
            echo "</div>";
            
        }
    } else {
        echo "<p>No tasks found.</p>";
    }

    $stmt->close();
    ?>
</ul>
<p class="mt-3 text-right ">Total Tasks Available: <?php echo $totalTasksCount; ?></p>

<!-- Task Status Handling Code -->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["task_completed"])) {
    // Get the task ID from the form
    $task_id = $_POST["task_completed"];

    // Toggle the status (completed or not completed) in the database
    $sql = "UPDATE tasks SET is_completed = NOT is_completed WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $task_id, $user_id);

    if ($stmt->execute()) {
        echo "<p>Status updated successfully!</p>";
    } else {
        echo "<p>Error updating status: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>


    </div>

    <script>
        function updateStatus(taskId) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'status.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var button = document.getElementById('status_button_' + taskId);
            var statusTextElement = document.getElementById('status_text_' + taskId);
            if (xhr.responseText === '1') {
                button.innerHTML = 'Mark as Completed';
                statusTextElement.innerHTML = 'Status: Completed';
            } else {
                button.innerHTML = 'Mark as Not Completed';
                statusTextElement.innerHTML = 'Status: Not Completed';
            }
        }
    };
    xhr.send('task_id=' + taskId);
}
   </script>
 
 <script>

        function updateProgress(taskId) {
            var newProgress = document.getElementById('task_progress_' + taskId).value;
            
            // Use AJAX to update the progress
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'progress.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // If the update was successful, you can update the UI
                    console.log('Progress updated successfully for Task ID: ' + taskId);
                    
                    // Update the button text based on the new progress
                    var button = document.getElementById('update_button_' + taskId);
                    var statusText = (newProgress === 'Completed') ? 'Not Completed' : 'Completed';
                    button.innerHTML = 'Mark as ' + statusText;
                    
                    // Update the 'Status' text on the page
                    var statusTextElement = document.getElementById('status_text_' + taskId);
                    statusTextElement.innerHTML = 'Status: ' + statusText;
                }
            };
    xhr.send('task_id=' + taskId + '&new_progress=' + newProgress);
}

</script>

</body>
</html>