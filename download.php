<?php
include 'conn.php';

// ✅ Check kung may `folder_name` na pinasa
if (!isset($_GET['folder_name'])) {
    die("No folder selected.");
}

$selected_folder = $_GET['folder_name'];

// ✅ Extract Barangay name at Year (Assuming format: Barangay_Year)
$folder_parts = explode("_", $selected_folder);
$barangay_name = isset($folder_parts[0]) ? $folder_parts[0] : "Unknown";
$year = isset($folder_parts[1]) ? $folder_parts[1] : date("Y");

// ✅ Generate unique table name for the folder
$table_name = "users_" . preg_replace("/[^a-zA-Z0-9_]/", "", $selected_folder);

// ✅ Set headers para mag-download ng Word file
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment; filename=Masterlist_{$barangay_name}_{$year}.doc");
header("Pragma: no-cache");
header("Expires: 0");

// ✅ Start HTML for Word Document
echo "<html><meta charset='UTF-8'><body>";
echo "<h2 style='text-align: center;'>MASTERLIST OF PERSONS WITH DISABILITIES</h2>";
echo "<h3 style='text-align: center;'>Barangay: " . htmlspecialchars($barangay_name) . "</h3>";
echo "<h3 style='text-align: center;'>Rosario, Cavite</h3>";
echo "<h3 style='text-align: center;'>Year: " . htmlspecialchars($year) . "</h3>";
echo "<br>";

// ✅ Create table
echo "<table border='1' cellspacing='0' cellpadding='5' width='100%'>";
echo "<tr style='background-color: #f2f2f2; text-align: center;'>
        <th>Name</th>
        <th>Street</th>
        <th>Date of Birth</th>
        <th>Control ID</th>
        <th>Age</th>
        <th>Gender</th>
        <th>Disability</th>
      </tr>";

// ✅ Fetch data from dynamic table
$result = $conn->query("SELECT * FROM `$table_name`");
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['name']}</td>
            <td>{$row['street']}</td>
            <td>{$row['dob']}</td>
            <td>{$row['control_id']}</td>
            <td>{$row['age']}</td>
            <td>{$row['gender']}</td>
            <td>{$row['disability']}</td>
          </tr>";
}

echo "</table>";
echo "</body></html>";

?>
