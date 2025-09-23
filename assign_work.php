<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';
include 'admin_header.php';
include 'admin_sidebar.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_id = isset($_POST['faculty_id']) ? intval($_POST['faculty_id']) : 0;
    $date = $_POST['date'] ?? '';
    $time_from = $_POST['time_from'] ?? '';
    $time_to = $_POST['time_to'] ?? '';
    $domain = $_POST['domain'] ?? '';

    if ($faculty_id && $date && $time_from && $time_to && $domain) {
        $checkFaculty = mysqli_query($conn, "SELECT id FROM users WHERE id=$faculty_id AND role='faculty'");
        if (mysqli_num_rows($checkFaculty) > 0) {
            $assigned_by = $_SESSION['user_id'];
            $sql = "INSERT INTO worklogs (faculty_id, date, time_from, time_to, domain, description, status, remarks, assigned_by) 
                    VALUES ('$faculty_id', '$date', '$time_from', '$time_to', '$domain', '', 'Pending', '', '$assigned_by')";
            if (mysqli_query($conn, $sql)) {
                $success = "Work assigned successfully ✅";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        } else {
            $error = "Invalid faculty selected ❌";
        }
    } else {
        $error = "Please fill all fields ⚠️";
    }
}

$faculty_result = mysqli_query($conn, "SELECT id, name FROM users WHERE role='faculty'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assign Work - Admin</title>
<style>
body { font-family: 'Segoe UI', sans-serif; margin:0; background-color:#f5f5f5; }
.main-content { margin-left:220px; margin-top:70px; padding:20px; min-height:calc(100vh - 70px); }
.page-title { font-size:22px; font-weight:bold; margin-bottom:20px; color:#333; }
.form-container { background:#fff; padding:25px; border-radius:10px; max-width:650px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
table { width:100%; border-collapse:collapse; }
td { padding:10px; vertical-align:top; }
td label { display:block; font-weight:500; margin-bottom:5px; color:#444; }
td input, td select { width:100%; padding:6px 10px; border-radius:6px; border:1px solid #ccc; font-size:14px; box-sizing:border-box; }

/* Placeholder color for inputs, selects, textarea */
input::placeholder,
textarea::placeholder {
    color: #666;
    opacity: 1;
}
select option[disabled] {
    color: #666;
}

.time-input-group { display:flex; gap:5px; }
.time-input-group select { flex:1; }
.submit-btn { margin-top:15px; text-align:center; }
.submit-btn button { padding:8px 25px; border:none; border-radius:6px; background-color:#5a62ea; color:#fff; font-size:14px; cursor:pointer; }
.submit-btn button:hover { background-color:#4c54d4; }
.success { color:green; text-align:center; margin-bottom:10px; }
.error { color:red; text-align:center; margin-bottom:10px; }

@media screen and (max-width:768px){ .main-content { margin-left:0; padding:15px; } .time-input-group { flex-direction:column; } }
</style>
</head>
<body>

<div class="main-content">
    <div class="page-title">Assign Work to Faculty</div>

    <div class="form-container">
        <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST" action="" id="assign-work-form">
            <table>
                <tr>
                    <td>
                        <label>Select Faculty:</label>
                        <select name="faculty_id" required>
                            <option value="" disabled selected>-- Select Faculty --</option>
                            <?php while($row = mysqli_fetch_assoc($faculty_result)) { ?>
                                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td>
                        <label>Date:</label>
                        <input type="date" name="date" placeholder="Select date" required>
                    </td>
                </tr>

                <tr>
                    <td>
                        <label>From Time:</label>
                        <div class="time-input-group">
                            <select id="from-hour" required>
                                <option value="" disabled selected>Hour</option>
                                <?php for($i=1;$i<=12;$i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                            <select id="from-minute" required>
                                <option value="" disabled selected>Minute</option>
                                <?php for($i=0;$i<60;$i+=5): ?>
                                    <option value="<?= sprintf('%02d',$i) ?>"><?= sprintf('%02d',$i) ?></option>
                                <?php endfor; ?>
                            </select>
                            <select id="from-ampm" required>
                                <option value="" disabled selected>AM/PM</option>
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                            </select>
                            <input type="hidden" name="time_from" id="from-time-hidden">
                        </div>
                    </td>

                    <td>
                        <label>To Time:</label>
                        <div class="time-input-group">
                            <select id="to-hour" required>
                                <option value="" disabled selected>Hour</option>
                                <?php for($i=1;$i<=12;$i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                            <select id="to-minute" required>
                                <option value="" disabled selected>Minute</option>
                                <?php for($i=0;$i<60;$i+=5): ?>
                                    <option value="<?= sprintf('%02d',$i) ?>"><?= sprintf('%02d',$i) ?></option>
                                <?php endfor; ?>
                            </select>
                            <select id="to-ampm" required>
                                <option value="" disabled selected>AM/PM</option>
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                            </select>
                            <input type="hidden" name="time_to" id="to-time-hidden">
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <label>Domain:</label>
                        <input type="text" name="domain" placeholder="Enter domain" required>
                    </td>
                </tr>
            </table>

            <div class="submit-btn">
                <button type="submit">Assign Work</button>
            </div>
        </form>
    </div>
</div>

<script>
// Convert 12h to 24h format
function convertTo24Hour(hour, minute, ampm) {
    hour = parseInt(hour);
    minute = parseInt(minute);
    if(ampm === 'PM' && hour !== 12) hour += 12;
    if(ampm === 'AM' && hour === 12) hour = 0;
    return `${hour.toString().padStart(2,'0')}:${minute.toString().padStart(2,'0')}:00`;
}

document.getElementById('assign-work-form').addEventListener('submit', function(e){
    const fH = document.getElementById('from-hour').value;
    const fM = document.getElementById('from-minute').value;
    const fA = document.getElementById('from-ampm').value;
    const tH = document.getElementById('to-hour').value;
    const tM = document.getElementById('to-minute').value;
    const tA = document.getElementById('to-ampm').value;

    if(!fH || !fM || !fA || !tH || !tM || !tA){
        e.preventDefault();
        alert("Please select From and To time completely!");
        return;
    }

    document.getElementById('from-time-hidden').value = convertTo24Hour(fH,fM,fA);
    document.getElementById('to-time-hidden').value = convertTo24Hour(tH,tM,tA);
});
</script>

</body>
</html>
