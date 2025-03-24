<?php
include 'conn.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE id=$id");
$row = $result->fetch_assoc();
?>

<form action="update.php" method="POST">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    <input type="text" name="name" value="<?= $row['name'] ?>" required>
    <input type="text" name="street" value="<?= $row['street'] ?>" required>
    <input type="date" name="dob" value="<?= $row['dob'] ?>" required>
    <input type="text" name="control_id" value="<?= $row['control_id'] ?>" required>
    <input type="number" name="age" value="<?= $row['age'] ?>" required>
    <select name="gender">
        <option value="Male" <?= ($row['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= ($row['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
    </select>
    <input type="text" name="disability" value="<?= $row['disability'] ?>">
    <button type="submit">Update</button>
</form>
