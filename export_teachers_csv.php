<?php
include('includes/db.php');

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="teachers_list.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write column headers
fputcsv($output, ['ID', 'Name', 'Mobile', 'Email']);

// Fetch data from database
$result = $db->query("SELECT id, name, mobile, email FROM teachers");

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    fputcsv($output, [$row['id'], $row['name'], $row['mobile'], $row['email']]);
}

fclose($output);
exit;
?>

