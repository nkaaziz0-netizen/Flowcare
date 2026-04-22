<?php
session_start();
include("../config/config.php");

if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard/dashboard.php");
    exit();
}

$result = $conn->query("SELECT * FROM logs ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>System Logs</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f5f7fa; }
.card {
    border-radius:16px;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
}
</style>

</head>
<body>

<div class="container mt-5">

<div class="card p-4">

<h4 class="mb-4">📊 System Logs</h4>

<table class="table table-hover">
<thead>
<tr>
<th>User</th>
<th>Action</th>
<th>Time</th>
</tr>
</thead>

<tbody>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['username'] ?></td>
<td>
<?php if($row['action'] == "login"): ?>
<span class="badge bg-success">Login</span>
<?php else: ?>
<span class="badge bg-danger">Logout</span>
<?php endif; ?>
</td>
<td><?= $row['created_at'] ?></td>
</tr>
<?php endwhile; ?>

</tbody>

</table>

</div>

</div>

</body>
</html>