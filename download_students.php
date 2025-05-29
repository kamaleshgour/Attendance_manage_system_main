<?php
include('includes/db.php');

// Set headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="students_list.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write column headers
fputcsv($output, ['ID', 'Roll No', 'Name', 'Father Name', 'DOB', 'Gender', 'Course', 'Branch', 'Semester', 'Mobile', 'Email']);

// Fetch student data
$result = $db->query("SELECT id, roll_no, name, father_name, dob, gender, course, branch, semester, mobile, email FROM students");

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    fputcsv($output, [
        $row['id'],
        $row['roll_no'],
        $row['name'],
        $row['father_name'],
        $row['dob'],
        $row['gender'],
        $row['course'],
        $row['branch'],
        $row['semester'],
        $row['mobile'],
        $row['email']
    ]);
}

fclose($output);
exit;
?>
