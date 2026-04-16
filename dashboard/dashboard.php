<?php
session_start();
include("../config/config.php");

if(!isset($_SESSION['username'])){
header("Location: login.php");
exit();
}
?>

<!DOCTYPE html>
<html>
<head>

<title>FlowCare Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="wrapper">

<!-- SIDEBAR -->

<div class="sidebar">

<h3 class="logo">FlowCare</h3>

<ul class="nav flex-column">

<li class="nav-item">
<a class="nav-link text-white" href="#">
<i class="bi bi-speedometer2"></i> Dashboard
</a>
</li>

<li class="nav-item">
<a class="nav-link text-white" href="../queue/queue.php">
<i class="bi bi-tv"></i> Queue Display
</a>
</li>

<li class="nav-item">
<a class="nav-link text-white" href="#">
<i class="bi bi-people"></i> Patients
</a>
</li>

<li class="nav-item">
<a class="nav-link text-white" href="../authentication/logout.php">
<i class="bi bi-box-arrow-right"></i> Logout
</a>
</li>

</ul>

</div>

<ul class="nav flex-column">

<li class="nav-item">
<a class="nav-link text-white" href="../dashboard/dashboard.php">
<i class="bi bi-speedometer2"></i> Dashboard
</a>
</li>

<li class="nav-item">
<a class="nav-link text-white" href="../queue/queue.php">
<i class="bi bi-tv"></i> Queue Display
</a>
</li>

<?php if($_SESSION['role']=="doctor"){ ?>
<li class="nav-item">
<a class="nav-link text-white" href="../queue/next_patient.php">
<i class="bi bi-megaphone"></i> Call Patient
</a>
</li>
<?php } ?>

<?php if($_SESSION['role']=="admin"){ ?>
<li class="nav-item">
<a class="nav-link text-white" href="logs.php">  <!-- x buat lagi -->
<i class="bi bi-clock-history"></i> System Logs
</a>
</li>
<?php } ?>

<li class="nav-item mt-3">
<a class="nav-link text-white" href="../authentication/logout.php">
<i class="bi bi-box-arrow-right"></i> Logout
</a>
</li>

</ul>


<!-- MAIN CONTENT -->

<div class="main-content">

<div class="container-fluid p-4">

<!-- TOPBAR -->

<div class="topbar mb-4">

<h4>Welcome <?php echo $_SESSION['username']; ?> 👋</h4>

</div>




<!-- DASHBOARD CARDS -->

<div class="row g-4">

<div class="col-md-3">

<div class="dashboard-card">

<h6>Now Serving</h6>

<div id="serving" class="queue-number">-</div>

</div>

</div>


<div class="col-md-3">

<div class="dashboard-card">

<h6>Waiting Patients</h6>

<div id="waiting_count" class="queue-number">0</div>

</div>

</div>


<div class="col-md-3">

<div class="dashboard-card">

<h6>Estimated Time</h6>

<div id="estimated_time" class="queue-number">0m</div>

</div>

</div>


<div class="col-md-3">

<div class="dashboard-card">

<h6>Total Patients</h6>

<div id="total_patients" class="queue-number">0</div>

</div>

</div>

</div>


<!-- ACTION BUTTONS -->

<div class="mt-4">

<?php if($_SESSION['role'] == "doctor"){ ?>

<a href="../queue/next_patient.php" class="btn btn-success btn-lg me-3">
<i class="bi bi-megaphone"></i> Call Next Patient
</a>

<?php } ?>

<a href="../queue/queue.php" class="btn btn-primary btn-lg">
<i class="bi bi-tv"></i> View Queue Display
</a>

</div>


<!-- WAITING QUEUE TABLE -->

<div class="table-card mt-5">

<h5 class="mb-3">Waiting Queue</h5>

<table class="table table-hover">

<thead>

<tr>
<th>Queue Number</th>
<th>Status</th>
</tr>

</thead>

<tbody id="waiting_list"></tbody>

</table>

</div>

</div>

</div>

</div>


<script>

function loadQueue(){

fetch("../queue/get_queue.php")

.then(res => res.json())

.then(data => {

// update dashboard cards
document.getElementById("serving").innerText = data.serving;

document.getElementById("waiting_count").innerText = data.waiting.length;

document.getElementById("estimated_time").innerText = data.estimated_wait + "m";

// update queue table
let table = document.getElementById("waiting_list");

table.innerHTML = "";

data.waiting.forEach(function(queue){

let row = `
<tr>
<td>${queue}</td>
<td><span class="badge bg-warning text-dark">Waiting</span></td>
</tr>
`;

table.innerHTML += row;

});

})

.catch(error => console.log("Queue error:", error));

}

// load immediately
loadQueue();

// refresh every 3 seconds
setInterval(loadQueue,3000);

</script>

</body>
</html>