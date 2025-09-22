<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'admin_sidebar.php';
include 'db.php';

$query = "
    SELECT w.*, u.name AS faculty_name, u.department
    FROM worklogs w
    JOIN users u ON w.faculty_id = u.id
    ORDER BY w.date DESC, w.time_from DESC
";
$logs = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Report - All Logs</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f8;
            margin: 0;
        }
        .main-content {
            margin-left: 220px;
            padding: 20px 25px;
        }
        h1 {
            color: #333;
            margin-bottom: 14px;
            font-size: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            font-size: 13px;
        }
        th, td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #828794ff;
            color: white;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .status {
            font-weight: 600;
        }
        .Approved { color: green; }
        .Rejected { color: red; }
        .Pending { color: orange; }

        .btn-group {
            display: flex;
            gap: 5px;
        }
        .btn {
            padding: 4px 8px;
            border: none;
            font-size: 12px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            cursor: pointer;
        }
        .approve { background-color: #60e264ff; }
        .reject { background-color: #ec554bff; }
        .edit { background-color: #3b6588ff; }

        .approve:hover { background-color: #43a047; }
        .reject:hover { background-color: #e53935; }
        .edit:hover { background-color: #1976d2; }

        .remarks {
            font-size: 12px;
            color: #555;
        }
        .delete {
            background-color: #808080; /* grey */
        }
        .delete:hover {
            background-color: #666666; /* darker grey on hover */
        }

    </style>
</head>
<body>

<div class="main-content">
    <h1>Admin Report - All Faculty Logs</h1>

    <table>
        <thead>
            <tr>
                <th>Faculty</th>
                <th>Department</th>
                <th>Date</th>
                <th>Time</th>
                <th>Domain</th>
                <th>Description</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $logs->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['faculty_name']) ?></td>
                <td><?= htmlspecialchars($row['department']) ?></td>
                <td><?= htmlspecialchars($row['date']) ?></td>
                <td><?= htmlspecialchars($row['time_from']) ?> - <?= htmlspecialchars($row['time_to']) ?></td>
                <td><?= htmlspecialchars($row['domain']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td class="status <?= htmlspecialchars($row['status']) ?>">
                    <?= htmlspecialchars($row['status']) ?>
                </td>
                <td class="remarks"><?= htmlspecialchars($row['remarks']) ?></td>
                <td>
                <div class="btn-group">
                    <a href="admin_worklog_update.php?id=<?= $row['id'] ?>&action=approve" class="btn approve">Approve</a>
                    <a href="admin_worklog_update.php?id=<?= $row['id'] ?>&action=reject" class="btn reject">Reject</a>
                    <a href="admin_edit_worklog.php?id=<?= $row['id'] ?>" class="btn edit">Edit</a>
                    <a href="admin_worklog_update.php?id=<?= $row['id'] ?>&action=delete" class="btn delete" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</a>

                </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
