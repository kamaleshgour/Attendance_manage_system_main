<?php
include('includes/db.php');

// USERS Table
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    password TEXT NOT NULL,
    role TEXT CHECK(role IN ('admin', 'teacher', 'student')) NOT NULL
)");

// STUDENTS Table
$db->exec("CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    roll_no TEXT UNIQUE NOT NULL,
    name TEXT,
    father_name TEXT,
    dob TEXT,
    gender TEXT,
    course TEXT,
    branch TEXT,
    semester TEXT,
    mobile TEXT,
    email TEXT,
    password TEXT
)");

// TEACHERS Table
$db->exec("CREATE TABLE IF NOT EXISTS teachers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    mobile TEXT,
    email TEXT,
    password TEXT
)");

// COURSES Table
$db->exec("CREATE TABLE IF NOT EXISTS courses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    course_name TEXT,
    subject TEXT
)");

// ATTENDANCE Table
$db->exec("CREATE TABLE IF NOT EXISTS attendance (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER,
    teacher_id INTEGER,
    course_name TEXT,
    date TEXT,
    status TEXT CHECK(status IN ('Present', 'Absent')),
    FOREIGN KEY(student_id) REFERENCES students(id),
    FOREIGN KEY(teacher_id) REFERENCES teachers(id)
)");
// Course assignment table
$db->exec("CREATE TABLE IF NOT EXISTS course_assignments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    course_name TEXT,
    student_id INTEGER,
    teacher_id INTEGER,
    FOREIGN KEY(student_id) REFERENCES students(id),
    FOREIGN KEY(teacher_id) REFERENCES teachers(id)
)");


// Insert default admin
$db->exec("INSERT OR IGNORE INTO users (username, password, role) 
           VALUES ('admin', 'admin123', 'admin')");

// Insert sample teacher to both tables
$db->exec("INSERT OR IGNORE INTO teachers (name, mobile, email, password)
           VALUES ('John Smith', '9876543210', 'john@college.edu', 'teach123')");
$db->exec("INSERT OR IGNORE INTO users (username, password, role)
           VALUES ('john@college.edu', 'teach123', 'teacher')");

// Insert sample student to both tables
$db->exec("INSERT OR IGNORE INTO students (roll_no, name, semester, branch, mobile, email, password)
           VALUES ('STU001', 'Alice Sharma', '4', 'CSE', '9123456789', 'alice@student.edu', 'stud123')");
$db->exec("INSERT OR IGNORE INTO users (username, password, role)
           VALUES ('student1', 'stud123', 'student')");
// Assign student to teacher for English
// First, fetch student and teacher IDs
$teacherID = $db->querySingle("SELECT id FROM teachers WHERE email = 'john@college.edu'");
$studentID = $db->querySingle("SELECT id FROM students WHERE email = 'alice@student.edu'");

if ($teacherID && $studentID) {
    $db->exec("INSERT OR IGNORE INTO course_assignments (course_name, student_id, teacher_id)
               VALUES ('English', $studentID, $teacherID)");
}
// For deleting last entry
// $db->exec("DELETE FROM users WHERE id = (SELECT MAX(id) FROM users)");


echo "✅ All tables created and sample users (admin, teacher, student) added.";
?>
