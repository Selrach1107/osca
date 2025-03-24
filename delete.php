<?php
include 'conn.php';

if (isset($_GET['id']) && isset($_GET['table'])) {
    $id = $_GET['id'];
    $table_name = $_GET['table'];
    $conn->query("DELETE FROM `$table_name` WHERE id = $id");
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
?>
