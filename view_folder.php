<?php
include 'conn.php';

// ✅ Check kung may folder_name sa URL
if (!isset($_GET['folder_name'])) {
    die("No folder selected.");
}

$selected_folder = $_GET['folder_name'];

// ✅ Gumawa ng unique at secured table name (sanitized)
$table_name = "users_" . preg_replace("/[^a-zA-Z0-9_]/", "", $selected_folder);

// ✅ Siguraduhin na ang table ay unique para sa bawat folder
$sql_create_table = "CREATE TABLE IF NOT EXISTS `$table_name` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    street VARCHAR(255) NOT NULL,
    dob DATE NOT NULL,
    control_id VARCHAR(50) NOT NULL,
    age INT NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    disability VARCHAR(255) NOT NULL
)";

if (!$conn->query($sql_create_table)) {
    die("Error creating table: " . $conn->error);
}

// ✅ Process form submission kung may POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $street = $_POST['street'];
    $dob = $_POST['dob'];
    $control_id = $_POST['control_id'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $disability = $_POST['disability'];

    // 🔒 Gumamit ng prepared statements para maiwasan ang SQL injection
    $stmt = $conn->prepare("INSERT INTO `$table_name` (name, street, dob, control_id, age, gender, disability) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiss", $name, $street, $dob, $control_id, $age, $gender, $disability);

    if ($stmt->execute()) {
        echo "<script>alert('New user added successfully!'); window.location.href='view_folder.php?folder_name=$selected_folder';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// ✅ Fetch all users from the unique table
$result = $conn->query("SELECT * FROM `$table_name`");

// ✅ Search functionality
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT * FROM `$table_name` WHERE name LIKE ? OR control_id LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_search = "%$search%";
    $stmt->bind_param("ss", $like_search, $like_search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM `$table_name`");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Folder: <?php echo htmlspecialchars($selected_folder); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">


<!-- ✅ Button to Open the Modal -->
<button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
    Add New User
</button>

<!-- ✅ Bootstrap Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Name:</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Street:</label>
                        <input type="text" name="street" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date of Birth:</label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Control ID No.:</label>
                        <input type="text" name="control_id" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Age:</label>
                        <input type="number" name="age" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gender:</label>
                        <select name="gender" class="form-control" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Disability:</label>
                        <input type="text" name="disability" class="form-control" required>
                    </div>

                    <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<a href="index.php" class="btn btn-primary mb-3">Back</a>


<!-- ✅ Search Bar -->
<input type="text" id="search" class="form-control mb-3" placeholder="Search by Name or Control ID">
<div id="searchResults"></div>


<!-- ✅ Display Table -->
<h3>PWD List of <?php echo htmlspecialchars($selected_folder); ?></h3>
<table id="mainTable" class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Name</th>
            <th>Street</th>
            <th>Date of Birth</th>
            <th>Control ID No.</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Disability</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['street']); ?></td>
                <td><?php echo htmlspecialchars($row['dob']); ?></td>
                <td><?php echo htmlspecialchars($row['control_id']); ?></td>
                <td><?php echo htmlspecialchars($row['age']); ?></td>
                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                <td><?php echo htmlspecialchars($row['disability']); ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $row['id']; ?>&table=<?php echo $table_name; ?>&folder_name=<?php echo urlencode($selected_folder); ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete.php?id=<?php echo $row['id']; ?>&table=<?php echo $table_name; ?>" onclick="return confirm('Are you sure?');" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<form method="get" action="download.php">
    <input type="hidden" name="folder_name" value="<?php echo $selected_folder; ?>">
    <button type="submit" class="btn btn-secondary">Download as Word</button>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('search').addEventListener('input', function() {
    let query = this.value.trim(); // Alisin ang extra spaces
    let table_name = "<?php echo $table_name; ?>";
    let searchResults = document.getElementById('searchResults');
    let mainTable = document.getElementById('mainTable'); // Siguraduhin na may ID ito sa HTML

    if (query.length > 0) {
        fetch(`search.php?query=${query}&table_name=${table_name}`)
            .then(response => response.text())
            .then(data => {
                searchResults.innerHTML = `
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Street</th>
                                <th>Date of Birth</th>
                                <th>Control ID No.</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Disability</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>${data}</tbody>
                    </table>
                `;
                mainTable.style.display = "none"; // Itago ang original table
            });
    } else {
        searchResults.innerHTML = ""; // Linisin ang search results
        mainTable.style.display = "table"; // Ipakita ulit ang buong table kapag walang laman ang search
    }
});
</script>




</body>
</html>
