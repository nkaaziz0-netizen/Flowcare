<?php
session_start();
include("../config/config.php");

if ($_SESSION['role'] != 'admin') {
    header("Location: ../dashboard/dashboard.php");
    exit();
}

$actionFilter = $_GET['action'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM logs WHERE 1";
$params = [];
$types = "";

if (!empty($actionFilter)) {
    $query .= " AND action = ?";
    $params[] = $actionFilter;
    $types .= "s";
}

if (!empty($search)) {
    $query .= " AND username LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    if ($time < 60) return "$time sec ago";
    if ($time < 3600) return floor($time/60) . " min ago";
    if ($time < 86400) return floor($time/3600) . " hrs ago";
    return floor($time/86400) . " days ago";
}
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

.table thead th {
    position: sticky;
    top: 0;
    background: #fff;
}

tr:hover {
    background:#eef4ff !important;
}

.filters input, .filters select {
    border-radius:10px;
}

.badge {
    padding:6px 10px;
    font-size:12px;
}


</style>
</head>

<body>

<div class="container mt-5">

<div class="card p-4">

<div class="mb-2">
    <button onclick="goBack()" class="btn btn-outline-secondary btn-sm px-3">
        ← Back
    </button>
</div>

<h4 class="mb-4">📊 System Logs</h4>

<form method="GET" class="row g-3 filters mb-3"> <!-- filters-->
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search user..." value="<?= $search ?>">
    </div>

    <div class="col-md-3">
        <select name="action" class="form-control">
            <option value="">All Actions</option>
            <option value="login" <?= $actionFilter=='login'?'selected':'' ?>>Login</option>
            <option value="logout" <?= $actionFilter=='logout'?'selected':'' ?>>Logout</option>
        </select>
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary w-100">Filter</button>
    </div>

    <div class="col-md-2">
        <a href="logs.php" class="btn btn-secondary w-100">Reset</a>
    </div>
</form>

<div class="table-responsive" style="max-height:500px; overflow:auto;"> <!--Table -->
<table class="table table-hover align-middle">
<thead>
<tr>
<th>User</th>
<th>Action</th>
<th>Time</th>
</tr>
</thead>

<tbody id="logTable">

<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['username']) ?></td>

<td>
<?php if($row['action'] == "login"): ?>
<span class="badge bg-success">Login</span>
<?php else: ?>
<span class="badge bg-danger">Logout</span>
<?php endif; ?>
</td>

<td>
<?= $row['created_at'] ?><br>
<small class="text-muted"><?= timeAgo($row['created_at']) ?></small>
</td>
</tr>
<?php endwhile; ?>

</tbody>

</table>
</div>
</div>
</div>

<script>
function goBack() {
    if (document.referrer !== "") {
        window.history.back();
    } else {
        window.location.href = "../dashboard/dashboard.php";
    }
}
</script>

</body>
</html>