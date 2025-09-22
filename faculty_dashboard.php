<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit();
}
include 'db.php';
include 'faculty_sidebar.php';

$user_id = $_SESSION['user_id'];

// Fetch worklogs with department
$query = "
    SELECT w.*, u.department
    FROM worklogs w
    JOIN users u ON w.faculty_id = u.id
    WHERE w.faculty_id = ?
    ORDER BY w.date DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Faculty Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .main-content {
            margin-left: 220px;
            padding: 40px 30px;
            min-height: 100vh;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 26px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #828794ff;
            color: white;
        }
        tr:hover {
            background-color: #f2f2f2;
        }
        .status.approved {
            color: green;
            font-weight: bold;
        }
        .status.rejected {
            color: red;
            font-weight: bold;
        }
        .status.pending {
            color: gray;
            font-weight: bold;
        }
        .btn-edit {
            padding: 6px 12px;
            background-color: #406388ff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-edit:hover {
            background-color: #366ca5ff;
        }
    </style>
</head>
<body>

<div class="main-content">
    <h1>My Worklogs</h1>
    <table>
        <tr>
            <th>Department</th>
            <th>Date</th>
            <th>From</th>
            <th>To</th>
            <th>Domain</th>
            <th>Description</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Action</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars($row['time_from']) ?></td>
                    <td><?= htmlspecialchars($row['time_to']) ?></td>
                    <td><?= htmlspecialchars($row['domain']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td class="status <?= strtolower($row['status']) ?>">
                        <?= htmlspecialchars($row['status']) ?>
                    </td>
                    <td><?= htmlspecialchars($row['remarks']) ?></td>
                    <td>
                        <?php if (strtolower($row['status']) === 'rejected') { ?>
                            <form method="GET" action="faculty_edit_worklog.php" style="margin: 0;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn-edit">Edit</button>
                            </form>
                        <?php } else { ?>
                            <span style="color: #888;">Done</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php else: ?>
            <tr>
                <td colspan="9" style="text-align: center; padding: 20px;">No worklogs found.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
