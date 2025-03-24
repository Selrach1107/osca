<?php
include 'conn.php';
$base_dir = 'folders/';
if (!is_dir($base_dir)) {
    mkdir($base_dir);
}

// Create Folder
if (isset($_POST['create_folder'])) {
    $folder_name = trim($_POST['folder_name']);
    $year = trim($_POST['year']);
    $full_path = $base_dir . $folder_name . '_' . $year;
    
    if (!empty($folder_name) && !empty($year) && !is_dir($full_path)) {
        mkdir($full_path);
        $stmt = $conn->prepare("INSERT INTO folders (folder_name, year) VALUES (?, ?)");
        $stmt->bind_param("si", $folder_name, $year);
        $stmt->execute();
    }
}

// Rename Folder
if (isset($_POST['rename_folder'])) {
    $old_name = $_POST['old_name'];
    $new_name = $_POST['new_name'];
    $year = $_POST['year'];
    $new_path = $base_dir . $new_name . '_' . $year;
    
    if (is_dir($base_dir . $old_name) && !is_dir($new_path)) {
        rename($base_dir . $old_name, $new_path);
        $stmt = $conn->prepare("UPDATE folders SET folder_name = ?, year = ? WHERE CONCAT(folder_name, '_', year) = ?");
        $stmt->bind_param("sis", $new_name, $year, $old_name);
        $stmt->execute();
    }
}


// Delete Folder and its Corresponding Table
if (isset($_POST['delete_folder'])) {
    $folder_to_delete = $_POST['folder_name'];
    $delete_path = $base_dir . $folder_to_delete;

    if (is_dir($delete_path)) {
        // ✅ Tanggalin ang laman ng folder bago i-delete ang folder
        array_map('unlink', glob("$delete_path/*.*")); 
        rmdir($delete_path);

        // ✅ Delete entry from database
        $stmt = $conn->prepare("DELETE FROM folders WHERE CONCAT(folder_name, '_', year) = ?");
        $stmt->bind_param("s", $folder_to_delete);
        $stmt->execute();

        // ✅ Delete corresponding table from database
        $table_name = "users_" . preg_replace("/[^a-zA-Z0-9_]/", "", $folder_to_delete);
        $sql_drop = "DROP TABLE IF EXISTS `$table_name`";

        if ($conn->query($sql_drop) === TRUE) {
            echo "<script>alert('Folder and its table deleted successfully!'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Error deleting table: " . $conn->error . "'); window.history.back();</script>";
        }
    }
}


if (isset($_GET['folder_name'])) {
    $selected_folder = $_GET['folder_name'];
    $folder_path = $base_dir . $selected_folder;

    echo "<h3 class='mt-4'>Contents of: $selected_folder</h3>";

    if (is_dir($folder_path)) {
        $files = scandir($folder_path);
        echo "<table class='table table-bordered'>";
        echo "<thead><tr><th>File Name</th></tr></thead><tbody>";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "<tr><td>$file</td></tr>";
            }
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='text-danger'>Folder not found.</p>";
    }
}

// Search Functionality
$search_query = "";
$result = null;

if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM folders WHERE folder_name LIKE ? OR year LIKE ?");
    $like_search = "%" . $search_query . "%";
    
    $stmt->bind_param("ss", $like_search, $like_search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM folders");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Folder Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center">Folder Management</h2>
    <form action="" method="POST">
        <div class="row">
            <div class="col-md-4">
                <label>Folder Name:</label>
                <input type="text" name="folder_name" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Year:</label>
                <input type="text" name="year" class="form-control" required>
            </div>
            <div class="col-md-4 mt-4">
                <button type="submit" name="create_folder" class="btn btn-primary">Create Folder</button>
            </div>
        </div>
    </form>
    
    <!-- Search Bar -->
<form action="" method="GET" class="mt-4">
    <div class="input-group">
        <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search by Folder Name or Year" value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit" class="btn btn-secondary">Search</button>
        <button type="button" class="btn btn-danger" onclick="clearSearch()">Clear</button>
    </div>
</form>


    
<h3 class="mt-4">Existing Folders</h3>
<ul class="list-group">
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $folder_name = htmlspecialchars($row['folder_name'] . '_' . $row['year']);
            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                    <a href='view_folder.php?folder_name=$folder_name' target='_blank' class='text-decoration-none'>$folder_name</a>
                    <span>
                        <form action='' method='POST' class='d-inline'>
                            <input type='hidden' name='folder_name' value='$folder_name'>
                            <button type='submit' name='delete_folder' class='btn btn-danger btn-sm'>Delete</button>
                        </form>
                        <form action='' method='POST' class='d-inline'>
                            <input type='hidden' name='old_name' value='{$row['folder_name']}_{$row['year']}'>
                            <input type='text' name='new_name' placeholder='New Name' required>
                            <input type='text' name='year' placeholder='New Year' required>
                            <button type='submit' name='rename_folder' class='btn btn-warning btn-sm'>Rename</button>
                        </form>
                    </span>
                </li>";
        }
    } else {
        echo "<li class='list-group-item text-danger'>No folders found.</li>";
    }
    ?>
</ul>


</div>

<script>
function clearSearch() {
    document.getElementById("searchInput").value = ""; // Clear input field
    window.location.href = window.location.pathname; // Reload page without query parameters
}
</script>
</body>
</html>
