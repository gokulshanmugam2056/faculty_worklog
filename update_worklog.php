<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$user_id = $_SESSION['user_id']; // ensure this is available

// Sanitize and get form data
$date = $_POST['date'];
$from_time = $_POST['from_time'];
$to_time = $_POST['to_time'];
$domain = $_POST['domain'];
$description = $_POST['description'];

// Insert into worklogs
$stmt = $conn->prepare("INSERT INTO worklogs (faculty_id, date, time_from, time_to, domain, description, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
$stmt->bind_param("isssss", $user_id, $date, $from_time, $to_time, $domain, $description);

if ($stmt->execute()) {
    header("Location: my_worklogs.php?success=1");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
?>
