<?php
session_start();

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'admin_sidebar.php';
include 'admin_header.php';
include 'db.php';

// ✅ Handle status update (Approve/Reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['worklog_id'], $_POST['status'], $_POST['admin_remarks'])) {
    $worklog_id = intval($_POST['worklog_id']);
    $status = $_POST['status'];
    $remarks = trim($_POST['admin_remarks']);

    if (in_array($status, ['Approved', 'Rejected'])) {
        $stmt = $conn->prepare("UPDATE worklogs SET status = ?, remarks = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $remarks, $worklog_id);
        $stmt->execute();
    }
}

// ✅ Fetch worklogs after handling form submission
$query = "
    SELECT w.id, w.date, w.time_from, w.time_to, w.domain, w.description, w.status, w.remarks AS admin_remarks,
           u.name AS faculty_name, u.department
    FROM worklogs w
    JOIN users u ON w.faculty_id = u.id
    WHERE w.status IN ('Pending', 'Rejected')
    ORDER BY w.date DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Worklogs</title>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f4f6f8;
        margin: 0;
    }
    .main-content {
        margin-left: 220px;   /* keeps space for sidebar */
        margin-top: 50px;    /* move content upward */
        padding: 10px 25px;   /* reduced padding */
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

    select, input[type="text"] {
        padding: 4px 6px;
        font-size: 12px;
        border-radius: 4px;
        border: 1px solid #ccc;
        max-width: 120px;
    }
    .submit-btn {
        padding: 5px 10px;
        border: none;
        background-color: #4CAF50;
        color: white;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }
    .edit-icon {
        font-size: 13px;
        text-decoration: none;
        color: #e67e22;
    }
    .form-inline {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
    }
</style>
</head>
<body>

<div class="main-content">
    <h1>Faculty Worklogs - Review Panel</h1>

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
        <?php while ($row = $result->fetch_assoc()): ?>
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
                <td><?= htmlspecialchars($row['admin_remarks']) ?></td>
                <td>
                    <?php if ($row['status'] === 'Pending'): ?>
                        <form method="POST" class="form-inline">
                            <input type="hidden" name="worklog_id" value="<?= $row['id'] ?>">
                            <select name="status" required>
                                <option value="">Select</option>
                                <option value="Approved">Approve</option>
                                <option value="Rejected">Reject</option>
                            </select>
                            <input type="text" name="admin_remarks" placeholder="Remarks" required>
                            <button class="submit-btn" type="submit">Submit</button>
                        </form>
                    <?php elseif ($row['status'] === 'Rejected'): ?>
                        <a class="edit-icon" href="admin_edit_worklog.php?id=<?= $row['id'] ?>">Edit</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
