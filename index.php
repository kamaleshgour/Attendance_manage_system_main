<?php
include('includes/db.php');
session_start();

$error = '';  // Ensure it's always defined

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);

    // Check if username, password, and role exist
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :u AND password = :p AND role = :r");
    $stmt->bindValue(':u', $username, SQLITE3_TEXT);
    $stmt->bindValue(':p', $password, SQLITE3_TEXT);
    $stmt->bindValue(':r', $role, SQLITE3_TEXT);

    $res = $stmt->execute();
    $row = $res->fetchArray(SQLITE3_ASSOC);

    if ($row) {
        // Set session
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        // Redirect based on role
        if ($role === 'admin') {
            header("Location: dashboard.php"); // Admin panel
        } elseif ($role === 'teacher') {
            header("Location: dashboard_teacher.php"); // ✅ Teacher panel
        } elseif ($role === 'student') {
            header("Location: dashboard_student.php"); // ✅ Student panel (optional)
        }
        exit;
    } else {
        $error = "❌ Invalid $role login credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Multi-Login - Attendance System</title>
    <link rel="stylesheet" href="assets/css/style.css">
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background: url('assets/images/edtech-slider.png') no-repeat center center fixed;
        background-size: cover;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        min-height: 100vh;
    }

    h2 {
        margin-top: 40px;
        color: #ffffff;
        background-color: rgba(0, 0, 0, 0.5);
        padding: 10px 20px;
        border-radius: 10px;
    }

    .button-group {
        margin: 40px auto 20px auto;
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .button-group button {
        padding: 12px 25px;
        font-weight: bold;
        background-color:rgb(111, 120, 129);
        border: none;
        color: white;
        border-radius: 5px;
        cursor: pointer;
    }

    .button-group button:hover {
        background-color: #0056b3;
    }

    .login-form {
        display: none;
        margin: 20px auto;
        background-color: rgba(255, 255, 255, 0.9);
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px #ccc;
        width: 300px;
    }

    .login-form input {
        width: 90%;
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .login-form input[type="submit"] {
        background-color: #007BFF;
        color: white;
        border: none;
        font-weight: bold;
        cursor: pointer;
    }

    .login-form input[type="submit"]:hover {
        background-color: #0056b3;
    }

    .error {
        color: red;
        font-size: 14px;
        margin-bottom: 10px;
    }
</style>
<script>
    function showLogin(role) {
        document.querySelectorAll('.login-form').forEach(form => form.style.display = 'none');
        document.getElementById(role + '-form').style.display = 'block';
    }

    // Auto-show last submitted role form (after reload)
    window.onload = function() {
        const lastRole = "<?php echo isset($_POST['role']) ? $_POST['role'] : ''; ?>";
        if (lastRole) {
            showLogin(lastRole);
        }
    };
</script>

</head>
<body>

    <h2>Attendance Management System</h2>

    <div class="button-group">
        <button onclick="showLogin('admin')">Admin Login</button>
        <button onclick="showLogin('teacher')">Teacher Login</button>
        <button onclick="showLogin('student')">Student Login</button>
    </div>

    <!-- Admin Login Form -->
    <div class="login-form" id="admin-form">
        <h3>Admin Login</h3>
        <?php if ($error && isset($_POST['role']) && $_POST['role'] == 'admin') echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="hidden" name="role" value="admin">
            <input type="text" name="username" placeholder="Admin Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login as Admin">
        </form>
    </div>

    <!-- Teacher Login Form -->
    <div class="login-form" id="teacher-form">
        <h3>Teacher Login</h3>
        <?php if ($error && isset($_POST['role']) && $_POST['role'] == 'teacher') echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="hidden" name="role" value="teacher">
            <input type="text" name="username" placeholder="Teacher Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login as Teacher">
        </form>
    </div>

    <!-- Student Login Form -->
    <div class="login-form" id="student-form">
        <h3>Student Login</h3>
        <?php if ($error && isset($_POST['role']) && $_POST['role'] == 'student') echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="hidden" name="role" value="student">
            <input type="text" name="username" placeholder="Student Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login as Student">
        </form>
    </div>

</body>
</html>