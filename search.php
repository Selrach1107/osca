<?php
include 'conn.php';

if (isset($_GET['query']) && isset($_GET['table_name'])) {
    $query = $_GET['query'];
    $table_name = $_GET['table_name'];

    $sql = "SELECT * FROM `$table_name` 
            WHERE name LIKE ? OR control_id LIKE ?";
    
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$query%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['street']}</td>
                <td>{$row['dob']}</td>
                <td>{$row['control_id']}</td>
                <td>{$row['age']}</td>
                <td>{$row['gender']}</td>
                <td>{$row['disability']}</td>
                <td>
                    <a href='edit.php?id={$row['id']}&table={$table_name}' class='btn btn-warning btn-sm'>Edit</a>
                    <a href='delete.php?id={$row['id']}&table={$table_name}' onclick='return confirm(\"Are you sure?\");' class='btn btn-danger btn-sm'>Delete</a>
                </td>
              </tr>";
    }

    $stmt->close();
}
?>
