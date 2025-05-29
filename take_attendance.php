<?php
session_start();
include('includes/db.php');

// ✅ Redirect if not a teacher
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'teacher') {
    header("Location: index.php");
    exit;
}

$username = $_SESSION['username'];

// ✅ Get teacher ID
$teacherQuery = $db->prepare("SELECT id FROM teachers WHERE name = :username");
$teacherQuery->bindValue(':username', $username, SQLITE3_TEXT);

$teacherResult = $teacherQuery->execute()->fetchArray(SQLITE3_ASSOC);
$teacher_id = $teacherResult['id'] ?? null;

$message = '';
$students = [];
$course = $_POST['course'] ?? null;

// ✅ If attendance was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status']) && isset($_POST['date']) && $course) {
    $date = $_POST['date'];
    $statuses = $_POST['status'];

    foreach ($statuses as $student_id => $status) {
        $stmt = $db->prepare("INSERT INTO attendance (student_id, teacher_id, course_name, date, status)
                              VALUES (:student_id, :teacher_id, :course, :date, :status)");
        $stmt->bindValue(':student_id', $student_id, SQLITE3_INTEGER);
        $stmt->bindValue(':teacher_id', $teacher_id, SQLITE3_INTEGER);
        $stmt->bindValue(':course', $course, SQLITE3_TEXT);
        $stmt->bindValue(':date', $date, SQLITE3_TEXT);
        $stmt->bindValue(':status', $status, SQLITE3_TEXT);
        $stmt->execute();
    }

    $message = "✅ Attendance saved for $course on $date.";
}

// ✅ If course is selected, fetch students assigned to it
if ($course && empty($message)) {
    $stmt = $db->prepare("SELECT s.id, s.name FROM students s
                          JOIN course_assignments ca ON s.id = ca.student_id
                          WHERE ca.course_name = :course AND ca.teacher_id = :teacher_id");
    $stmt->bindValue(':course', $course, SQLITE3_TEXT);
    $stmt->bindValue(':teacher_id', $teacher_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $students[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Attendance</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        h2 { color: #333; }
        label, select, input[type="date"] {
            margin: 10px 0;
            font-size: 16px;
        }
        .student-list { margin-top: 20px; }
        .student-list label { display: block; margin-bottom: 8px; }
        .message { color: green; font-weight: bold; }
        input[type="submit"] {
            margin-top: 20px;
            padding: 10px 20px;
            font-weight: bold;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #1f8b3b;
        }
        .back-button {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #5a6268;
        }

    </style>
</head>
<body>

<h2>Take Attendance</h2>

<?php if ($message): ?>
    <p class="message"><?= $message ?></p>
<?php endif; ?>

<!-- Step 1: Select course -->
<form method="POST">
    <label for="course">Select Subject:</label>
    <select name="course" id="course" onchange="this.form.submit()" required>
        <option value="">-- Choose a subject --</option>
        <option value="English" <?= $course == 'English' ? 'selected' : '' ?>>English</option>
        <option value="Maths" <?= $course == 'Maths' ? 'selected' : '' ?>>Maths</option>
        <option value="Science" <?= $course == 'Science' ? 'selected' : '' ?>>Science</option>
    </select>
</form>

<?php if (!empty($students)): ?>
<!-- Step 2: Show attendance form -->
<form method="POST">
    <input type="hidden" name="course" value="<?= htmlspecialchars($course) ?>">

    <label for="date">Select Date:</label>
    <input type="date" name="date" required>

    <div class="student-list">
        <h4>Mark Attendance:</h4>
        <?php foreach ($students as $student): ?>
            <label>
                <?= htmlspecialchars($student['name']) ?>:
                <select name="status[<?= $student['id'] ?>]">
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                </select>
            </label>
        <?php endforeach; ?>
    </div>

    <input type="submit" value="Save Attendance">
</form>
<?php elseif ($course): ?>
    <p><em>No students assigned to <?= htmlspecialchars($course) ?>.</em></p>
<?php endif; ?>
<a href="dashboard_teacher.php" class="back-button">⬅ Go Back</a>
</body>
</html>
