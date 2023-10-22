<!DOCTYPE html>
<html>
<head>
    <title>Add Task - Task Tracker</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="">
</head>

<style>

body {background-color: #f0f0f0; /* Change the background color to your preference */
  font-family: 'Arial', sans-serif; /* Change the font to your preference */
  background-image: url('Asset/todolistbg.jpg');
  background-size: cover;
 }
 
 .bg-white{
    background:transparent;
    border:2px solid rgba(255,255,255,0.9); 
    border-radius:20px;
    backdrop-filter:blur(100px);
 }

</style>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="container mx-auto mt-20 p-4 py-4 bg-white rounded hover: white" style="max-width: 650px;">
        <h2 class="text-2xl font-semibold mb-4">Add Task</h2>
        <?php
       session_start();

       // Check if the user is logged in
       if (!isset($_SESSION["email"])) {
           header("Location: to-do.php");
           exit;
       }
       
       // Database connection setup (replace with your connection code)
       $db = new mysqli("localhost", "root", "", "task_tracker");
       
       if ($db->connect_error) {
           die("Connection failed: " . $db->connect_error);
       }
       
       // Process the form when submitted
       if ($_SERVER["REQUEST_METHOD"] == "POST") {
           if (isset($_POST["title"]) && isset($_POST["due_date"])) {
               $title = $_POST["title"];
               $description = isset($_POST["description"]) ? $_POST["description"] : null;
               $due_date = $_POST["due_date"];
       
               // Check if title and due_date are not empty
               if (empty($title) || empty($due_date)) {
                   echo "<p class='text-red-500'>Required form fields are missing.</p>";
               } else {
                   // Get the user's ID from the session
                   $email = $_SESSION["email"];
                   $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
                   $stmt->bind_param("s", $email);
                   $stmt->execute();
                   $stmt->bind_result($user_id);
                   $stmt->fetch();
                   $stmt->close();
       
                   // Insert the task into the tasks table
                   $sql = "INSERT INTO tasks (title, description, due_date, user_id) VALUES (?, ?, ?, ?)";
                   $stmt = $db->prepare($sql);
       
                   if (!$stmt) {
                       echo "<p class='text-red-500'>Error preparing statement: " . $db->error . "</p>";
                   } else {
                       $stmt->bind_param("sssi", $title, $description, $due_date, $user_id);
       
                       if ($stmt->execute()) {
                           echo "<p class='text-green-500'>Task added successfully!</p>";
                       } else {
                           echo "<p class='text-red-500'>Error adding task: " . $stmt->error . "</p>";
                       }
       
                       $stmt->close();
                   }
               }
           } else {
               echo "<p class='text-red-500'>Required form fields are missing.</p>";
           }
       }
       ?>
         <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Title:</label>
                <input type="text" name="title" class="form-input mt-1 w-full" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                <textarea name="description" class="form-textarea mt-1 w-full"></textarea>
            </div>

            <div class="mb-4">
                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date:</label>
                <input type="date" name="due_date" class="form-input mt-1 w-full" required>
            </div>

            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600">Add Task</button>
        </form>
        <p class="mt-4"><a href="to-do.php" class="text-blue-500">Back to Task List</a></p>
    </div>
</body>
</html>
