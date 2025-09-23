<?php
// Highlight current page
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
/* Sidebar container */
.sidebar {
    width: 200px; /* same as faculty */
    background: #f5f6ffff;
    color: #000;
    padding-top: 80px; /* offset for fixed header */
    padding-left: 15px;
    padding-right: 15px;
    padding-bottom: 20px;
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    box-shadow: 2px 0 8px rgba(0,0,0,0.1);
    z-index: 999;
    overflow-y: auto;
}

/* Sidebar links */
.sidebar a {
    display: block;
    color: #000;
    background-color: transparent;
    padding: 12px 16px;
    margin: 8px 0;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    transition: background 0.3s ease, transform 0.2s ease;
}

/* Hover effect */
.sidebar a:hover {
    background-color: #d3d3d3; /* grey hover */
    transform: translateX(3px);
}

/* Active link */
.sidebar a.active {
    background-color: rgba(122, 122, 122, 0.25);
    font-weight: bold;
}

/* Optional: small header inside sidebar */
.sidebar .sidebar-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #000;
    text-align: center;
}
</style>

<div class="sidebar">
    <a href="admin_dashboard.php" class="<?= ($currentPage == 'admin_dashboard.php') ? 'active' : '' ?>">Dashboard</a>
    <a href="assign_work.php" class="<?= ($currentPage == 'assign_work.php') ? 'active' : '' ?>">Assign Work</a>
    <a href="admin_report.php" class="<?= ($currentPage == 'admin_report.php') ? 'active' : '' ?>">Report</a>
    <a href="logout.php" class="<?= ($currentPage == 'logout.php') ? 'active' : '' ?>">Logout</a>
</div>
