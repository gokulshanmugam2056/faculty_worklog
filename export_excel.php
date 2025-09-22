<?php
include 'db.php';
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=logs.xls");

echo "Faculty\tDate\tFrom\tTo\tDomain\tDescription\tStatus\tRemarks\n";
$res = $conn->query("SELECT w.*, u.name FROM worklogs w JOIN users u ON w.faculty_id = u.id");
while ($row = $res->fetch_assoc()) {
    echo "{$row['name']}\t{$row['date']}\t{$row['time_from']}\t{$row['time_to']}\t{$row['domain']}\t{$row['description']}\t{$row['status']}\t{$row['remarks']}\n";
}
?>
