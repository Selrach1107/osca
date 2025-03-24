<?php
include 'conn.php';

if (isset($_POST['export'])) {
    $table_name = $_POST['table_name'];
    $barangay_name = $_POST['barangay_name'];
    $year = $_POST['year'];

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=masterlist_$barangay_name_$year.docx");

    echo "MASTERLIST OF PERSONS WITH DISABILITIES\n";
    echo "Barangay: $barangay_name\n";
    echo "Rosario, Cavite\n";
    echo "Year: $year\n\n";

    echo "Name\tStreet\tDate of Birth\tControl ID No.\tAge\tGender\tDisability\n";

    $result = $conn->query("SELECT * FROM `$table_name`");
    while ($row = $result->fetch_assoc()) {
        echo "{$row['name']}\t{$row['street']}\t{$row['dob']}\t{$row['control_id']}\t{$row['age']}\t{$row['gender']}\t{$row['disability']}\n";
    }

    exit();
}
?>
