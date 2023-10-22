<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
</head>

<style>
    body {background-color: #f0f0f0; /* Change the background color to your preference */
  font-family: 'Arial', sans-serif; /* Change the font to your preference */
  background-image: url('Asset/todolistbg.jpg');
  background-position: center;
  background-size: cover;
 }
    
</style>

<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="container mx-auto mt-10 p-5 bg-white rounded-lg shadow-lg" style="max-width: 550px;">
        <h2 class="text-center font-semibold mb-5">Edit Task</h2>

        <?php
        session_start();
        if (!isset($_SESSION["email"])) {
            header("Location: to-do.php");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
            $task_id = $_GET["id"];


            $db = new mysqli("localhost", "root", "", "task_tracker");
            $stmt = $db->prepare("SELECT description FROM tasks WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $task_id, $_SESSION["user_id"]);
            $stmt->execute();
            $stmt->bind_result($description);
            $stmt->fetch();
            $stmt->close();
        if(isset($task_id)){
            echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
            echo '<label class="block text-gray-700 text-sm font-bold mb-2" for="description">Deskripsi</label>';
            echo "<input type='text' name='id' value='" . $task_id . "' hidden>";
            echo '<textarea name="description" class="form-textarea mb-4" required cols="58">' . $description . '</textarea>';
            echo '<button type="submit" name="save_changes" class="bg-blue-500 text-white px-4 py-2 rounded hover-bg-blue-600">Simpan Perubahan Deskripsi</button>';
        } else {
            echo "<p class='text-red-500'>Tugas tidak ditemukan.</p>";
        }}
        
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["save_changes"])) {
            $db = new mysqli("localhost", "root", "", "task_tracker");
            $task_id = $_POST["id"];
            // echo "<pre>";
            // print_r($_SERVER);
            // print_r($_POST);
            // print_r($_SESSION) ;
            // echo "</pre>";
            $new_description = $_POST["description"]; // Ambil deskripsi yang telah diubah
            
            $stmt = $db->prepare("UPDATE tasks SET description = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sii", $new_description, $task_id, $_SESSION["user_id"]); 

            if ($stmt->execute()) {
                echo "<p class='text-green-500 mb-2'>Perubahan deskripsi berhasil disimpan.</p>";
                header("Location: to-do.php");
                exit;
            } else {
                echo "<p class='text-red-500 mb-2'>Gagal menyimpan perubahan.</p>";
            }

            $stmt->close();
        }
        ?>
    </div>
</body>
</html>