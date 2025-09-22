<?php
session_start();

// Include database connection
$host = "localhost";
$user = "root";
$password = "";       // no password
$database = "faculty_worklog";
$port = 3307;         // XAMPP MySQL port

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        // Use password_verify if passwords are hashed in DB
        if ($password === $user['password']) {  // plain text check
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['department'] = $user['department'];

            if ($user['role'] === 'faculty') {
                header("Location: faculty_dashboard.php");
                exit;
            } elseif ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
                exit;
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Worklog Login</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #6C63FF, #42A5F5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: #fff;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 320px;
        }

        .login-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .login-container label {
            font-weight: bold;
            color: #333;
            font-size: 0.9em;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 95%;
            padding: 8px;
            margin: 8px 0 18px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.9em;
        }

        .login-container input[type="text"]:focus,
        .login-container input[type="password"]:focus {
            border-color: #6C63FF;
            outline: none;
        }

        .login-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background: #6C63FF;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 0.95em;
            cursor: pointer;
        }

        .login-container input[type="submit"]:hover {
            background: #5a54d1;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 12px;
            font-size: 0.85em;
        }

        .footer {
            text-align: center;
            font-size: 0.8em;
            color: #888;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Faculty Worklog Login</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post" action="login.php" autocomplete="off">
            <label>Email</label>
            <input type="text" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <input type="submit" value="Login">
        </form>
        <div class="footer">© 2025 Faculty Worklog System</div>
    </div>
</body>
</html>
