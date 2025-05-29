<?php
include('includes/db.php');

$result = $db->query("SELECT id, name, mobile, email FROM teachers");

if (!$result || $result->numColumns() == 0) {
    echo "<p>No teachers found.</p>";
} else {
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>
            <tr><th>ID</th><th>Name</th><th>Mobile</th><th>Email</th></tr>";

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['mobile']}</td>
                <td>{$row['email']}</td>
              </tr>";
    }

    echo "</table>";
}
?>
