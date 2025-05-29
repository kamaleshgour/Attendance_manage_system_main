<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}



include('includes/db.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role']; // 'teacher' or 'student'

   if ($role === 'teacher') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);

    // Add to teachers table
    $result1 = $db->exec("INSERT INTO teachers (name, mobile, email, password)
                         VALUES ('$username', '$mobile', '$email', '$password')");

    // Add to users table
    $result2 = $db->exec("INSERT INTO users (username, password, role)
                         VALUES ('$username', '$password', 'teacher')");

    if ($result1 && $result2) {
        $message = "✅ Teacher added successfully!";
    } else {
        $message = "❌ Failed to add teacher. Error: " . $db->lastErrorMsg();
    }
}

        if ($role === 'student') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $name = $username;  // ✅ Make student.name = username
        $roll = trim($_POST['roll_no']);
        $semester = trim($_POST['semester']);
        $branch = trim($_POST['branch']);
        $email = trim($_POST['email']);
        $mobile = trim($_POST['mobile']);

        $db->exec("INSERT OR IGNORE INTO students (roll_no, name, semester, branch, mobile, email, password)
                VALUES ('$roll', '$name', '$semester', '$branch', '$mobile', '$email', '$password')");

        $db->exec("INSERT OR IGNORE INTO users (username, password, role)
                VALUES ('$username', '$password', 'student')");

        $message = "✅ Student added successfully!";
    }

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
       body {
    font-family: Arial, sans-serif;
    background: #f2f2f2;
    margin: 0;
    padding: 0;
    text-align: center;
    padding-top: 50px;
    position: relative;
    overflow: hidden;
}
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    overflow-y: auto; /* Enables vertical scrolling */
}


body::before {
    content: "";
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: url('assets/images/admin.jpg') no-repeat center center fixed;
    background-size: cover;
    filter: blur(8px);
    z-index: -1;
    pointer-events: none;  /* Prevent it from intercepting mouse/scroll */
}


        h2 {
            margin-bottom: 30px;
        }

        .btn-group button {
            padding: 12px 25px;
            margin: 10px;
            font-weight: bold;
            background-color: #007BFF;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-group button:hover {
            background-color: #0056b3;
        }

        .form-container {
            display: none;
            margin: 10px auto;
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px #ccc;
            width: 400px;
            text-align: left;
            max-height: 80vh;
            overflow-y: auto;

        }

        input[type="text"], input[type="password"], input[type="email"] {
            width: 95%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .message {
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }

        .logout-button {
        display: inline-block;
        padding: 10px 20px;
        background-color:rgb(74, 70, 70); /* red color */
        color: white;
        text-decoration: none;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        }

        .logout-button:hover {
        background-color: #cc0000; /* darker red on hover */
        }

    </style>
    <script>
        function toggleForm(id) {
            document.getElementById("teacher-form").style.display = "none";
            document.getElementById("student-form").style.display = "none";
            document.getElementById(id).style.display = "block";
        }
        function viewTeachers() {
            fetch('get_teachers.php')
                .then(response => response.text())
                .then(data => {
                    const container = document.getElementById('teacher-list');
                    container.innerHTML = data;
                    container.style.display = 'block';
                });
        }
    </script>
</head>
<body>

    <h2>Welcome, Admin (<?php echo $_SESSION['username']; ?>)</h2>

    <div class="btn-group">
        <button onclick="toggleForm('teacher-form')">Add Teacher</button>

        <button onclick="toggleForm('student-form')">Add Student</button>
    </div>

    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Teacher Form -->
    <div class="form-container" id="teacher-form">
        <h3>Add Teacher</h3>
        <form method="POST">
            <input type="hidden" name="role" value="teacher">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Login Username" required>
            <input type="password" name="password" placeholder="Login Password" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="mobile" placeholder="Mobile Number" required>
            <input type="submit" value="Add Teacher">
            <button type="button" onclick="viewTeachers()">View List</button>
            <a href="export_teachers_csv.php" class="button">Download CSV</a>
            <div id="teacher-list" style="display:none; margin-top:20px;"></div>

        </form>
    </div>

    <!-- Student Form -->
<div class="form-container" id="student-form">
    <h3>Add Student</h3>
    <form method="POST">
        <input type="hidden" name="role" value="student">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="roll_no" placeholder="Roll Number" required>
        <input type="text" name="username" placeholder="Login Username" required>
        <input type="password" name="password" placeholder="Login Password" required>
        <input type="text" name="semester" placeholder="Semester" required>
        <input type="text" name="branch" placeholder="Branch" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="mobile" placeholder="Mobile Number" required>
        <input type="submit" value="Add Student">
    </form>

    <!-- View List Button -->
    <div style="margin-top: 15px;">
        <a href="get_students.php" class="btn btn-info" target="_blank">View Student List</a>
    </div>

    <!-- Download CSV Button -->
    <div style="margin-top: 10px;">
        <form action="download_students.php" method="post">
            <button type="submit" class="btn btn-success">Download Student List</button>
        </form>
    </div>
</div>

    <a class="logout-button" href="logout.php">Logout</a>
</body>
</html>
