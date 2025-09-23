<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Restrict access to faculty only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: login.php");
    exit();
}

// Fetch faculty info
include 'db.php';
$user_id = $_SESSION['user_id'];

// Fetch name
$result = $conn->query("SELECT name FROM users WHERE id = $user_id");
$faculty_name = "Faculty"; // default
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $faculty_name = $row['name'];
}
?>

<style>
.header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 60px;
    background-color: #f5f6ffff;
    color: black; /* default text color */
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    z-index: 1000;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.header .title {
    font-size: 18px;
    font-weight: bold;
    color: black; /* title in black */
}

.header .profile-box {
    display: flex;
    align-items: center;
    gap: 8px;
    background-color: rgba(0, 0, 0, 0.05); /* light gray background */
    padding: 4px 10px;
    border-radius: 6px;
    max-width: 200px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    transform: translateX(-50px); /* slide backward */
}

.header .profile-box img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #ccc;
    object-fit: cover;
}

.header .profile-box span {
    font-size: 14px;
    color: black; /* profile name in black */
    overflow: hidden;
    text-overflow: ellipsis;
}

.main-content {
    margin-top: 70px;
}
</style>

<div class="header">
    <div class="title">Faculty Dashboard</div>
    <div class="profile-box">
        <img src="https://via.placeholder.com/32" alt="Profile Picture">
        <span><?= htmlspecialchars($faculty_name) ?></span>
    </div>
</div>
