<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit();
}

include 'db.php';

$id = $_POST['id'];
$date = $_POST['date'];
$time_from = $_POST['time_from'];
$time_to = $_POST['time_to'];
$domain = $_POST['domain'];
$description = $_POST['description'];
$faculty_id = $_SESSION['user_id'];

// Update query with status reset
$stmt = $conn->prepare("
    UPDATE worklogs 
    SET date = ?, time_from = ?, time_to = ?, domain = ?, description = ?, status = 'Pending', remarks = ''
    WHERE id = ? AND faculty_id = ?
");

$stmt->bind_param("ssssssi", $date, $time_from, $time_to, $domain, $description, $id, $faculty_id);

if ($stmt->execute()) {
    header("Location: faculty_dashboard.php?updated=1");
    exit();
} else {
    echo "Failed to update worklog: " . $stmt->error;
}
?>
