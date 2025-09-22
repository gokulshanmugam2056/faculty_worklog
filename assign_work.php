<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $date = $_POST['date'];
    $from_time = $_POST['from_time'];
    $to_time = $_POST['to_time'];
    $domain = $_POST['domain'];
    $description = $_POST['description'];

    $query = "INSERT INTO worklogs (user_id, date, from_time, to_time, domain, description, status)
              VALUES ('$user_id', '$date', '$from_time', '$to_time', '$domain', '$description', 'Assigned')";
    
    if (mysqli_query($conn, $query)) {
        $success = "Work assigned successfully!";
    } else {
        $error = "Error assigning work: " . mysqli_error($conn);
    }
}

// Fetch all faculty users
$faculty_result = mysqli_query($conn, "SELECT id, name FROM users WHERE role = 'faculty'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Work</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .form-container {
            background-color: #f4f6ff;
            padding: 30px;
            border-radius: 10px;
            width: 60%;
            margin: auto;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input, select, textarea {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        input[type="submit"] {
            background-color: #4e54c8;
            color: white;
            border: none;
            cursor: pointer;
        }
        .success { color: green; text-align: center; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main-content">
    <div class="form-container">
        <h2>Assign Work to Faculty</h2>

        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST" action="">
            <label for="user_id">Select Faculty:</label>
            <select name="user_id" required>
                <option value="">-- Select Faculty --</option>
                <?php while ($row = mysqli_fetch_assoc($faculty_result)) { ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                <?php } ?>
            </select>

            <label>Date:</label>
            <input type="date" name="date" required>

            <label>From Time:</label>
            <input type="time" name="from_time" required>

            <label>To Time:</label>
            <input type="time" name="to_time" required>

            <label>Domain:</label>
            <input type="text" name="domain" required>

            <label>Description:</label>
            <textarea name="description" rows="4" required></textarea>

            <input type="submit" value="Assign Work">
        </form>
    </div>
</div>

</body>
</html>
