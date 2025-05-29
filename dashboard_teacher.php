<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'teacher') {
    header("Location: index.php");
    exit;
}

// Get teacher ID from table (by matching email or name)
$username = $_SESSION['username'];
$teacherQuery = $db->prepare("SELECT id FROM teachers WHERE email = :email OR name = :name");
$teacherQuery->bindValue(':email', $username, SQLITE3_TEXT);
$teacherQuery->bindValue(':name', $username, SQLITE3_TEXT);
$teacherResult = $teacherQuery->execute()->fetchArray(SQLITE3_ASSOC);
$teacher_id = $teacherResult['id'] ?? null;

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course'], $_POST['students'])) {
    $course = $_POST['course'];
    $students = $_POST['students'];

    foreach ($students as $student_id) {
        $stmt = $db->prepare("INSERT INTO course_assignments (course_name, student_id, teacher_id)
                              VALUES (:course, :student_id, :teacher_id)");
        $stmt->bindValue(':course', $course, SQLITE3_TEXT);
        $stmt->bindValue(':student_id', $student_id, SQLITE3_INTEGER);
        $stmt->bindValue(':teacher_id', $teacher_id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    $message = "✅ Students assigned to $course.";
}

// Get list of all students
$studentsRes = $db->query("SELECT id, name FROM students");
$students = [];
while ($row = $studentsRes->fetchArray(SQLITE3_ASSOC)) {
    $students[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
            text-align: center;
            background-color: #f2f2f2;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: url('assets/images/course_assignment.jpg') no-repeat center center fixed;
            background-size: cover;
            filter: blur(15px);
            z-index: -1;
            pointer-events: none;  /* Prevent it from intercepting mouse/scroll */
        }
        h2 {
            color: #fff; /* white or light color for glow */
            text-shadow: 0 0 5px #f00, 0 0 10px #f00, 0 0 15px #f00, 0 0 20px #f00;
            font-weight: bold;
            transition: text-shadow 0.3s ease-in-out;
        }

        select, input[type="submit"] {
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
        }
        .students-list {
            margin: 20px 0;
        }
        .students-list label {
            display: block;
            margin: 5px;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .logout {
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
            color: red;
            font-weight: bold;
        }
        .btn-link {
            padding: 12px 25px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-link:hover {
            background-color: #0056b3;
            transform: scale(1.05); /* optional bounce effect */
        }
    </style>
</head>
<body>

    <h2>Welcome, <?php echo htmlspecialchars($username); ?> (Teacher)</h2>

    <form method="POST">
        <label for="course">Select Course:</label>
        <select name="course" id="course" required>
            <option value="">--Choose a course--</option>
            <option value="English">English</option>
            <option value="Maths">Maths</option>
            <option value="Science">Science</option>
        </select>

        <div class="students-list">
            <p><strong>Select students to assign:</strong></p>
            <?php foreach ($students as $student): ?>
                <label>
                    <input type="checkbox" name="students[]" value="<?php echo $student['id']; ?>">
                    <?php echo htmlspecialchars($student['name']); ?>
                </label>
            <?php endforeach; ?>
        </div>

        <input type="submit" value="Assign Students">
    </form>

    <?php if ($message): ?>
        <p class="success"><?php echo $message; ?></p>
    <?php endif; ?>

    <a class="logout" href="logout.php">Logout</a>
    <hr>
<a href="take_attendance.php" class="btn-link">
    ➕ Take Attendance
</a>


</body>
</html>
