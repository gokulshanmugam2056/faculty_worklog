<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "@gokultamil", "faculty_worklog");

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: admin_dashboard.php");
    exit();
}

$message = "";

// Fetch existing worklog data
$stmt = $conn->prepare("SELECT * FROM worklogs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$worklog = $result->fetch_assoc();

if (!$worklog) {
    die("Worklog not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $domain = $_POST['domain'];
    $time_from = $_POST['time_from'];
    $time_to = $_POST['time_to'];
    $remarks = $_POST['remarks'] ?? '';
    $status = $_POST['status'] ?? null;

    $update = $conn->prepare("UPDATE worklogs SET description = ?, domain = ?, time_from = ?, time_to = ?, remarks = ?" . ($status ? ", status = ?" : "") . " WHERE id = ?");
    
    if ($status) {
        $update->bind_param("ssssssi", $description, $domain, $time_from, $time_to, $remarks, $status, $id);
    } else {
        $update->bind_param("sssss", $description, $domain, $time_from, $time_to, $remarks);
        $update->bind_param("i", $id);
    }

    if ($update->execute()) {
        $message = "Worklog updated successfully.";
    } else {
        $message = "Failed to update worklog.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Worklog</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            margin: 0;
        }
        .main {
            margin-left: 220px;
            padding: 40px;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            padding: 10px 20px;
            font-size: 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 10px;
        }
        .approve { background-color: #4caf50; color: white; }
        .reject { background-color: #f44336; color: white; }
        .save { background-color: #0d47a1; color: white; }
        .message {
            margin-bottom: 15px;
            color: green;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
    <h2>Edit Worklog</h2>
    
    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($worklog['description']) ?></textarea>

        <label>Domain:</label>
        <input type="text" name="domain" value="<?= htmlspecialchars($worklog['domain']) ?>" required>

        <label>Time From:</label>
        <input type="time" name="time_from" value="<?= $worklog['time_from'] ?>" required>

        <label>Time To:</label>
        <input type="time" name="time_to" value="<?= $worklog['time_to'] ?>" required>

        <label>Remarks:</label>
        <textarea name="remarks"><?= htmlspecialchars($worklog['remarks']) ?></textarea>
        <button type="submit" class="approve" name="status" value="Approved">Approve</button>
        <button type="submit" class="reject" name="status" value="Rejected">Reject</button>
    </form>
</div>
</body>
</html>
