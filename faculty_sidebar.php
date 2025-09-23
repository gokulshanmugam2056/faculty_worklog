<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
.sidebar {
    width: 200px; /* reduced width */
    background: #f5f6ffff;
    color: #ffffff;
    padding-top: 80px; /* equal to header height */
    padding-left: 15px;
    padding-right: 15px;
    padding-bottom: 20px;
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
    z-index: 999;
    overflow-y: auto;
}

.sidebar a {
    display: block;
    color: #000000ff;
    background-color: transparent;
    padding: 12px 16px;
    margin: 8px 0;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    transition: background 0.3s ease, transform 0.2s ease;
}

/* Changed hover color to grey */
.sidebar a:hover {
    background-color: #d3d3d3; /* grey */
    transform: translateX(3px);
}

.sidebar a.active {
    background-color: rgba(122, 122, 122, 0.25);
    font-weight: bold;
}
</style>

<div class="sidebar">
    <a href="faculty_dashboard.php" class="<?= ($currentPage == 'faculty_dashboard.php') ? 'active' : '' ?>">Dashboard</a>
    <a href="submit_worklog.php" class="<?= ($currentPage == 'submit_worklog.php') ? 'active' : '' ?>">Submit Work</a>
    <a href="logout.php" class="<?= ($currentPage == 'logout.php') ? 'active' : '' ?>">Logout</a>
</div>
