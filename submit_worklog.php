<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit();
}

include 'faculty_sidebar.php';
include 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch faculty name and department
$sql = "SELECT name, department FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];
$department = $row['department'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST['date'];
    $from_time = $_POST['from_time'];
    $to_time = $_POST['to_time'];
    $domain = $_POST['domain'];
    $description = $_POST['description'];

    // Prepare insert statement
    $stmt = $conn->prepare("INSERT INTO worklogs (faculty_id, user_id, date, time_from, time_to, domain, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("iisssss", $user_id, $user_id, $date, $from_time, $to_time, $domain, $description);

    if ($stmt->execute()) {
        echo "<script>alert('Worklog submitted successfully'); window.location.href='faculty_dashboard.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!-- HTML + CSS -->
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f5f5f5;
    }

    .main-content {
        margin-left: 220px;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px;
        box-sizing: border-box;
    }

    .form-container {
        background: white;
        padding: 40px 50px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 750px;
    }

    .form-container h1 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 24px;
        color: #333;
    }

    .form-container label {
        display: block;
        margin-bottom: 4px;
        font-weight: 500;
        font-size: 14px;
        color: #444;
    }

    .form-container input,
    .form-container textarea {
        width: 100%;
        padding: 6px 10px;
        margin-bottom: 18px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 13px;
    }

    .time-row {
        display: flex;
        gap: 15px;
    }

    .time-row .time-group {
        flex: 1;
    }

    .form-container button {
        background-color: #5a62ea;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 15px;
        cursor: pointer;
        width: 100%;
    }

    .form-container button:hover {
        background-color: #4c54d4;
    }
</style>

<div class="main-content">
    <form action="" method="POST" class="form-container">
        <h1>Submit Work Log 📝</h1>

        <label>Faculty Name</label>
        <input type="text" name="faculty_name" value="<?= htmlspecialchars($name) ?>" readonly>

        <label>Department</label>
        <input type="text" name="department" value="<?= htmlspecialchars($department) ?>" readonly>

        <label>Date</label>
        <input type="date" name="date" required>

        <div class="time-row">
            <div class="time-group">
                <label>From Time</label>
                <input type="time" name="from_time" required>
            </div>
            <div class="time-group">
                <label>To Time</label>
                <input type="time" name="to_time" required>
            </div>
        </div>

        <label>Domain</label>
        <input type="text" name="domain" placeholder="Enter domain" required>

        <label>Description</label>
        <textarea name="description" placeholder="Enter work description" rows="4" required></textarea>

        <button type="submit">Submit Worklog</button>
    </form>
</div>
