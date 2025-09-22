<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit();
}
include 'db.php';

$log_id = $_GET['id'] ?? null;
if (!$log_id) {
    echo "Invalid request.";
    exit();
}

// Fetch the worklog
$stmt = $conn->prepare("SELECT * FROM worklogs WHERE id = ? AND faculty_id = ?");
$stmt->bind_param("ii", $log_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Worklog not found.";
    exit();
}

$log = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Worklog</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f1f1f1;
        }
        form {
            background: white;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        button {
            padding: 10px 20px;
            background: #4c54d4;
            color: white;
            border: none;
            border-radius: 5px;
        }
        button:hover {
            background: #4c54d4;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Edit Worklog</h2>
<form method="POST" action="faculty_worklog_update.php">
    <input type="hidden" name="id" value="<?= $log['id'] ?>">

    <label>Date:</label>
    <input type="date" name="date" value="<?= $log['date'] ?>" required>

    <label>From:</label>
    <input type="time" name="time_from" value="<?= $log['time_from'] ?>" required>

    <label>To:</label>
    <input type="time" name="time_to" value="<?= $log['time_to'] ?>" required>

    <label>Domain:</label>
    <input type="text" name="domain" value="<?= htmlspecialchars($log['domain']) ?>" required>

    <label>Description:</label>
    <textarea name="description" rows="4" required><?= htmlspecialchars($log['description']) ?></textarea>

    <button type="submit">Update Worklog</button>
</form>

</body>
</html>
