<?php
include 'conn.php';

if (!isset($_GET['folder_name'])) {
    die("Error: Missing folder_name in URL.");
}


$id = $_POST['id'];
$name = $_POST['name'];
$street = $_POST['street'];
$dob = $_POST['dob'];
$control_id = $_POST['control_id'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$disability = $_POST['disability'];

$sql = "UPDATE users SET name='$name', street='$street', dob='$dob', control_id='$control_id', age='$age', gender='$gender', disability='$disability' WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: index.php");
} else {
    echo "Error: " . $conn->error;
}
?>
