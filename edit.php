<?php
include 'conn.php';

if (!isset($_GET['id']) || !isset($_GET['table'])) {
    die("Invalid request.");
}

$id = $_GET['id'];
$table_name = $_GET['table'];

// Kunin ang kasalukuyang data
$result = $conn->query("SELECT * FROM `$table_name` WHERE id = $id");
if ($result->num_rows == 0) {
    die("Record not found.");
}
$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $street = $_POST['street'];
    $dob = $_POST['dob'];
    $control_id = $_POST['control_id'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $disability = $_POST['disability'];

    $sql = "UPDATE `$table_name` SET 
                name = ?, 
                street = ?, 
                dob = ?, 
                control_id = ?, 
                age = ?, 
                gender = ?, 
                disability = ? 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssissi", $name, $street, $dob, $control_id, $age, $gender, $disability, $id);
    
    if ($stmt->execute()) {
        header("Location: view_folder.php?folder_name=" . $_GET['folder_name']);
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Edit Record</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Street:</label>
            <input type="text" name="street" class="form-control" value="<?php echo $row['street']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Date of Birth:</label>
            <input type="date" name="dob" class="form-control" value="<?php echo $row['dob']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Control ID No.:</label>
            <input type="text" name="control_id" class="form-control" value="<?php echo $row['control_id']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Age:</label>
            <input type="number" name="age" class="form-control" value="<?php echo $row['age']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Gender:</label>
            <select name="gender" class="form-control">
                <option value="Male" <?php echo ($row['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($row['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Disability:</label>
            <input type="text" name="disability" class="form-control" value="<?php echo $row['disability']; ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="view_folder.php?<?php echo isset($_GET['folder_name']) ? 'folder_name=' . $_GET['folder_name'] : ''; ?>" class="btn btn-secondary">Cancel</a>
        </form>
</div>
</body>
</html>
