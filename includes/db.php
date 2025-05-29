<?php
$db = new SQLite3('attendance.db');  // Now it's relative to the current script's location
if (!$db) {
    die("Database connection failed.");
}
?>
