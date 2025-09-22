<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "@gokultamil", "faculty_worklog");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

// Delete action
if ($id && $action === 'delete') {
    $stmt = $conn->prepare("DELETE FROM worklogs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_report.php");
    exit();
}

// Approve or reject
if (!$id || !in_array($action, ['approve', 'reject'])) {
    header("Location: admin_report.php");
    exit();
}

$status = ($action === 'approve') ? 'Approved' : 'Rejected';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $remarks = trim($_POST['remarks']);
    $stmt = $conn->prepare("UPDATE worklogs SET status = ?, remarks = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $remarks, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_report.php");
    exit();
}

// Fetch existing remarks to prefill
$remarks = '';
$stmt = $conn->prepare("SELECT remarks FROM worklogs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($remarks);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= ucfirst($action) ?> Worklog</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
        }
        .main {
            margin-left: 220px;
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #0d47a1;
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
        }
        textarea {
            width: 100%;
            height: 120px;
            font-size: 14px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            resize: vertical;
            margin-bottom: 20px;
        }
        button {
            background-color: #0d47a1;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0b3c91;
        }
        .delete-btn {
            background-color: #c62828;
            margin-top: 10px;
        }
        .delete-btn:hover {
            background-color: #b71c1c;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
    <div class="container">
        <h2><?= ucfirst($action) ?> Worklog</h2>

        <form method="post">
            <label for="remarks">Remarks:</label><br>
            <textarea name="remarks" id="remarks" required><?= htmlspecialchars($remarks) ?></textarea><br>
            <button type="submit"><?= ucfirst($action) ?> Worklog</button>
        </form>

        <form method="get" onsubmit="return confirm('Are you sure you want to delete this worklog?');">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="delete-btn">Delete Worklog</button>
        </form>
    </div>
</div>
</body>
</html>
