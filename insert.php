<?php
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $street = $_POST['street'];
    $dob = $_POST['dob'];
    $control_id = $_POST['control_id'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $disability = $_POST['disability'];

    $sql = "INSERT INTO users (name, street, dob, control_id, age, gender, disability) 
            VALUES ('$name', '$street', '$dob', '$control_id', '$age', '$gender', '$disability')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
