<?php
include('includes/db.php');

$result = $db->query("SELECT id, roll_no, name, semester, branch, mobile, email FROM students");

echo "<table border='1' cellpadding='8'>
        <tr>
            <th>ID</th>
            <th>Roll No</th>
            <th>Name</th>
            <th>Semester</th>
            <th>Branch</th>
            <th>Mobile</th>
            <th>Email</th>
        </tr>";

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['roll_no']}</td>
            <td>{$row['name']}</td>
            <td>{$row['semester']}</td>
            <td>{$row['branch']}</td>
            <td>{$row['mobile']}</td>
            <td>{$row['email']}</td>
          </tr>";
}

echo "</table>";
?>
