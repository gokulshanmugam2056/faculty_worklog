<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include database
include 'db.php';

// Fetch admin name
$user_id = $_SESSION['user_id'];
$admin_name = "Admin"; // default
$result = $conn->query("SELECT name FROM users WHERE id = $user_id");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $admin_name = $row['name'];
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
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    z-index: 1000;
}

.header .title {
    font-size: 18px;
    font-weight: bold;
    color: black;
}

.header .profile-box {
    display: flex;
    align-items: center;
    gap: 8px;
    background-color: rgba(0,0,0,0.05);
    padding: 4px 10px;
    border-radius: 6px;
    max-width: 200px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.header .profile-box img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    background-color: #ccc;
}

.header .profile-box span {
    font-size: 14px;
    color: black;
    overflow: hidden;
    text-overflow: ellipsis;
}

.main-content {
    margin-top: 70px; /* offset for header */
    margin-left: 220px; /* sidebar width */
    padding: 20px 25px;
}
</style>

<div class="header">
    <div class="title"><a href="admin_dashboard.php" style="text-decoration:none; color:black;">Admin Dashboard</a></div>
    <div class="profile-box">
        <img src="https://via.placeholder.com/32" alt="Profile Picture">
        <span><?= htmlspecialchars($admin_name) ?></span>
    </div>
</div>
