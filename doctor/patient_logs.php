<?php
include("../config/config.php");

$logs = $conn->query("
    SELECT * FROM patient_logs 
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Patient Logs</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">

<h3>Patient Logs</h3>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>Queue Number</th>
            <th>Action</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody>

    <?php while($row = $logs->fetch_assoc()){ ?>
        <tr>
            <td><?= $row['queue_number'] ?></td>
            <td><?= $row['action'] ?></td>
            <td><?= $row['created_at'] ?></td>
        </tr>
    <?php } ?>

    </tbody>
</table>

</body>
</html>