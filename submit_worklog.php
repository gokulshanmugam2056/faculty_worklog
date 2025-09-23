<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: login.php");
    exit();
}

include 'faculty_sidebar.php';
include 'faculty_header.php';
include 'db.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT name, department FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$name = $row['name'];
$department = $row['department'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $date = $_POST['date'];
        
        // Convert 12-hour format to 24-hour format for database
        function convertTo24Hour($hour, $minute, $ampm) {
            $hour = intval($hour);
            if ($ampm === 'AM' && $hour === 12) {
                $hour = 0;
            } elseif ($ampm === 'PM' && $hour !== 12) {
                $hour += 12;
            }
            return sprintf('%02d:%s', $hour, $minute);
        }
        
        // Check if all required fields are present
        if (!isset($_POST['from_hour']) || !isset($_POST['from_minute']) || !isset($_POST['from_ampm']) ||
            !isset($_POST['to_hour']) || !isset($_POST['to_minute']) || !isset($_POST['to_ampm'])) {
            throw new Exception("Time fields are missing");
        }
        
        $from_time = convertTo24Hour($_POST['from_hour'], $_POST['from_minute'], $_POST['from_ampm']);
        $to_time = convertTo24Hour($_POST['to_hour'], $_POST['to_minute'], $_POST['to_ampm']);
        $domain = $_POST['domain'];
        $description = $_POST['description'];

        // Check database connection
        if (!$conn) {
            throw new Exception("Database connection failed");
        }

        $stmt = $conn->prepare("INSERT INTO worklogs (faculty_id, date, time_from, time_to, domain, description, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("isssss", $user_id, $date, $from_time, $to_time, $domain, $description);

        if ($stmt->execute()) {
            echo "<script>alert('Worklog submitted successfully'); window.location.href='faculty_dashboard.php';</script>";
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Worklog</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 240px;
            margin-top: 70px;
            padding: 20px 20px 40px 20px;
            min-height: calc(100vh - 70px);
            background-color: #f8f9fa;
        }

        .page-title {
            margin-bottom: 15px;
            font-size: 24px;
            color: #333;
            font-weight: bold;
        }

        .form-wrapper {
            display: flex;
            justify-content: center;
        }

        .form-container {
            background: white;
            padding: 15px 20px 0px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 950px;
            display: flex;
            flex-direction: column;
            min-height: fit-content;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        td {
            padding: 8px 12px;
            vertical-align: top;
        }

        td label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 14px;
            color: #444;
        }

        td input,
        td textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 13px;
            box-sizing: border-box;
            height: 35px;
        }

        .time-input-group {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .time-input-group select {
            padding: 8px 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 13px;
            height: 35px;
            background: white;
            color: #333;
        }

        .time-input-group select:invalid {
            color: #999;
        }

        .time-input-group select option[value=""] {
            color: #999;
        }

        .time-input-group select option:not([value=""]) {
            color: #333;
        }

        .time-input-group select:first-child {
            flex: 1;
        }

        .time-input-group select:nth-child(2) {
            flex: 1;
        }

        .time-input-group select:nth-child(3) {
            flex: 0.8;
        }

        .row {
            display: flex;
            gap: 10px;
        }

        .row td {
            width: 50%;
        }

        textarea {
            resize: none;
            height: 60px;
        }

        .submit-btn {
            margin-top: 8px;
            margin-bottom: 8px;
            text-align: right;
            align-self: flex-end;
        }

        .submit-btn button {
            background-color: #5a62ea;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        .submit-btn button:hover {
            background-color: #4c54d4;
        }

        @media screen and (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 10px;
            }

            .row {
                flex-direction: column;
            }

            .row td {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="page-title">Submit Work Log 📝</div>
    
    <div class="form-wrapper">
        <form action="" method="POST" class="form-container">
        <table>
            <tr class="row">
                <td>
                    <label>Faculty Name</label>
                    <input type="text" name="faculty_name" value="<?= htmlspecialchars($name) ?>" readonly>
                </td>
                <td>
                    <label>Department</label>
                    <input type="text" name="department" value="<?= htmlspecialchars($department) ?>" readonly>
                </td>
            </tr>
            <tr class="row">
                <td>
                    <label>Date</label>
                    <input type="date" name="date" id="worklog-date" required>
                </td>
                <td>
                    <label>Domain</label>
                    <input type="text" name="domain" placeholder="Enter domain" required>
                </td>
            </tr>
            <tr class="row">
                <td>
                    <label>From Time</label>
                    <div class="time-input-group">
                        <select name="from_hour" id="from-hour" required>
                            <option value="" style="color: #999;">00 Hour</option>
                            <?php for($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="from_minute" id="from-minute" required>
                            <option value="" style="color: #999;">00 Minute</option>
                            <?php for($i = 0; $i < 60; $i += 5): ?>
                                <option value="<?= sprintf('%02d', $i) ?>"><?= sprintf('%02d', $i) ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="from_ampm" id="from-ampm" required>
                            <option value="" style="color: #999;">AM/PM</option>
                            <option value="AM">AM</option>
                            <option value="PM">PM</option>
                        </select>
                        <input type="hidden" name="from_time" id="from-time-hidden">
                    </div>
                </td>
                <td>
                    <label>To Time</label>
                    <div class="time-input-group">
                        <select name="to_hour" id="to-hour" required>
                            <option value="" style="color: #999;">00 Hour</option>
                            <?php for($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="to_minute" id="to-minute" required>
                            <option value="" style="color: #999;">00 Minute</option>
                            <?php for($i = 0; $i < 60; $i += 5): ?>
                                <option value="<?= sprintf('%02d', $i) ?>"><?= sprintf('%02d', $i) ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="to_ampm" id="to-ampm" required>
                            <option value="" style="color: #999;">AM/PM</option>
                            <option value="AM">AM</option>
                            <option value="PM">PM</option>
                        </select>
                        <input type="hidden" name="to_time" id="to-time-hidden">
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label>Description</label>
                    <textarea name="description" placeholder="Enter work description"></textarea>
                </td>
            </tr>
        </table>

        <div class="submit-btn">
            <button type="submit">Submit Work</button>
        </div>
            </form>
    </div>
</div>

</body>

<script>
    // Set minimum date to today to prevent past dates
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('worklog-date');
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
        
        // Additional validation on form submit
        dateInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const currentDate = new Date();
            currentDate.setHours(0, 0, 0, 0);
            
            if (selectedDate < currentDate) {
                alert('You cannot select a past date. Please choose today or a future date.');
                this.value = '';
            }
        });

        // Convert 12-hour time to 24-hour format
        function convertTo24Hour(hour, minute, ampm) {
            let hour24 = parseInt(hour);
            
            if (ampm === 'AM' && hour24 === 12) {
                hour24 = 0;
            } else if (ampm === 'PM' && hour24 !== 12) {
                hour24 += 12;
            }
            
            return String(hour24).padStart(2, '0') + ':' + minute;
        }

        // Update hidden time fields
        function updateFromTime() {
            const hour = document.getElementById('from-hour').value;
            const minute = document.getElementById('from-minute').value;
            const ampm = document.getElementById('from-ampm').value;
            
            if (hour && minute && ampm) {
                const time24 = convertTo24Hour(hour, minute, ampm);
                document.getElementById('from-time-hidden').value = time24;
            }
        }

        function updateToTime() {
            const hour = document.getElementById('to-hour').value;
            const minute = document.getElementById('to-minute').value;
            const ampm = document.getElementById('to-ampm').value;
            
            if (hour && minute && ampm) {
                const time24 = convertTo24Hour(hour, minute, ampm);
                document.getElementById('to-time-hidden').value = time24;
                validateTimes();
            }
        }

        // Time validation
        function validateTimes() {
            const fromTime = document.getElementById('from-time-hidden').value;
            const toTime = document.getElementById('to-time-hidden').value;
            
            if (fromTime && toTime) {
                if (toTime <= fromTime) {
                    alert('To Time must be after From Time');
                    document.getElementById('to-hour').value = '';
                    document.getElementById('to-minute').value = '';
                    document.getElementById('to-ampm').value = '';
                    document.getElementById('to-time-hidden').value = '';
                }
            }
        }

        // Add event listeners
        document.getElementById('from-hour').addEventListener('change', updateFromTime);
        document.getElementById('from-minute').addEventListener('change', updateFromTime);
        document.getElementById('from-ampm').addEventListener('change', updateFromTime);
        
        document.getElementById('to-hour').addEventListener('change', updateToTime);
        document.getElementById('to-minute').addEventListener('change', updateToTime);
        document.getElementById('to-ampm').addEventListener('change', updateToTime);
    });
</script>

</html>