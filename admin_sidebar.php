<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        display: flex;
        background-color: #f4f7fc;
        min-height: 100vh;
    }

    .sidebar {
        width: 240px;
        background: linear-gradient(135deg, #828794ff, #828794ff);
        color: #ffffff;
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 40px;
        font-size: 24px;
        font-weight: bold;
    }

    .sidebar a {
        display: block;
        color: #ffffff;
        background-color: transparent;
        padding: 14px 18px;
        margin: 10px 0;
        text-decoration: none;
        border-radius: 8px;
        font-size: 15px;
        transition: background 0.3s ease, transform 0.2s ease;
    }

    .sidebar a:hover {
        background-color: rgba(255, 255, 255, 0.15);
        transform: translateX(5px);
    }

    .sidebar a.active {
        background-color: rgba(255, 255, 255, 0.25);
        font-weight: bold;
    }

    .main-content {
        margin-left: 240px;
        padding: 50px;
        flex: 1;
        background-color: #f4f7fc;
    }

    @media screen and (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            flex-direction: row;
            justify-content: space-around;
        }

        .main-content {
            margin-left: 0;
            padding: 20px;
        }
    }
</style>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php" class="<?= ($currentPage == 'admin_dashboard.php') ? 'active' : '' ?>">Dashboard</a>
    <a href="assign_work.php" class="<?= ($currentPage == 'assign_work.php') ? 'active' : '' ?>">Assign Work</a>
    <a href="admin_report.php" class="<?= ($currentPage == 'admin_report.php') ? 'active' : '' ?>">Report</a>
    <a href="logout.php" class="<?= ($currentPage == 'logout.php') ? 'active' : '' ?>">Logout</a>
</div>
