<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];

// Get student details from username
$studentQuery = $db->prepare("SELECT * FROM students WHERE name = :username");
$studentQuery->bindValue(':username', $username, SQLITE3_TEXT);
$student = $studentQuery->execute()->fetchArray(SQLITE3_ASSOC);

if (!$student) {
    echo "❌ Student not found.";
    exit;
}

$student_id = $student['id'];

$summary = [];

if (isset($_POST['show_summary'])) {
    $summaryQuery = $db->prepare("
        SELECT course_name, 
               COUNT(*) as total_classes,
               SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_count
        FROM attendance
        WHERE student_id = :student_id
        GROUP BY course_name
    ");
    $summaryQuery->bindValue(':student_id', $student_id, SQLITE3_INTEGER);
    $result = $summaryQuery->execute();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $summary[] = $row;
    }
}

// Fetch attendance records
$attendanceRes = $db->prepare("SELECT * FROM attendance WHERE student_id = :id");
$attendanceRes->bindValue(':id', $student_id, SQLITE3_INTEGER);
$attendanceRows = $attendanceRes->execute();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
            background: #f9f9f9;
        }
        h2 {
            color: #333;
        }
        .section {
            margin-bottom: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #aaa;
            text-align: center;
        }
        th {
            background-color: #ddd;
        }
        .logout {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 15px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<h2>Welcome, <?php echo htmlspecialchars($username); ?> (Student)</h2>

<div class="section">
    <h3>Your Profile:</h3>
    <p><strong>Roll No:</strong> <?= htmlspecialchars($student['roll_no']) ?></p>
    <p><strong>Branch:</strong> <?= htmlspecialchars($student['branch']) ?></p>
    <p><strong>Semester:</strong> <?= htmlspecialchars($student['semester']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
</div>

<div class="section">
    <h3>Your Attendance Records:</h3>
    <table>
        <tr>
            <th>Subject</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $attendanceRows->fetchArray(SQLITE3_ASSOC)): ?>
            <tr>
                <td><?= htmlspecialchars($row['course_name']) ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php if (!empty($summary)): ?>
    <h3>📊 Attendance Summary</h3>
    <table>
        <tr>
            <th>Subject</th>
            <th>Present</th>
            <th>Total Classes</th>
            <th>Attendance %</th>
        </tr>
        <?php foreach ($summary as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['course_name']) ?></td>
                <td><?= $row['present_count'] ?></td>
                <td><?= $row['total_classes'] ?></td>
                <td>
                    <?= $row['total_classes'] > 0 
                        ? round(($row['present_count'] / $row['total_classes']) * 100) . '%' 
                        : 'N/A' ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="show_summary" value="1">
    <input type="submit" value="📊 View Attendance Summary">
</form>

<a href="logout.php" class="logout">Logout</a>

</body>
</html>
