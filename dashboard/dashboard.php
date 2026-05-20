<?php
session_start();
include("../config/config.php");

/* PREVENT BACK CACHE */
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

/* CHECK LOGIN */
if(!isset($_SESSION['user_id'])){
    header("Location: ../authentication/login.php?error=loginfirst");
    exit();
}

if (!isset($_SESSION['role']) || 
   ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'doctor')) {
    echo "
    <script>
        alert('Access denied. Please login first');
        window.location.href='../authentication/login.php';
    </script>
    ";
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

<!navbar start >

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

<?php if($_SESSION['role'] == "admin"){ ?> <!-- admin to create user -->
<li class="nav-item">
<a class="nav-link text-white" href="../admin/create_user.php">
<i class="bi bi-person-plus"></i> Create User
</a>
</li> 
<?php } ?>

<?php if($_SESSION['role'] == "admin"){ ?> <!-- admin logs -->
<li class="nav-item">
<a class="nav-link text-white" href="../admin/logs.php">
<i class="bi bi-clock-history"></i> System Logs
</a>
</li>
<?php } ?>

<?php if($_SESSION['role'] == "doctor"){ ?>
<li class="nav-item">
<a class="nav-link text-white" href="../doctor/patient_logs.php">
<i class="bi bi-journal-text"></i> Patient Logs
</a>
</li>
<?php } ?>

<?php if($_SESSION['role'] == "doctor"){ ?>
<li class="nav-item">
<a class="nav-link text-white" href="../doctor/patients.php">
<i class="bi bi-people"></i> Patients
</a>
</li>
<?php } ?>

<li class="nav-item">
<a class="nav-link text-white"
   href="../authentication/logout.php"
   onclick="localStorage.clear();sessionStorage.clear();">
   <i class="bi bi-box-arrow-right"></i> Logout
</a>
</li>

</ul> <!--end of navbar -->

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

    <!-- Previous -->
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <h6>Previous</h6>
            <div id="previous" class="queue-number"></div>
        </div>
    </div>

    <!-- Now Serving -->
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <h6>Now Serving</h6>
            <div id="serving" class="queue-number">-</div>
        </div>
    </div>

    <!-- Next -->
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <h6>Next</h6>
            <div id="next" class="queue-number">-</div>
        </div>
    </div>

    <!-- Total -->
    <div class="col-md-3">
        <div class="dashboard-card text-center">
            <h6>Total Patients</h6>
            <div id="total_patients" class="queue-number">0</div>
        </div>
    </div>

    <!-- Waiting -->
    <div class="col-md-6">
        <div class="dashboard-card text-center">
            <h6>Waiting Patients</h6>
            <div id="waiting_count" class="queue-number">0</div>
        </div>
    </div>

    <!-- Estimated Time -->
    <div class="col-md-6">
        <div class="dashboard-card text-center">
            <h6>Estimated Time</h6>
            <div id="estimated_time" class="queue-number">0m</div>
        </div>
    </div>

</div>


<!-- ACTION BUTTONS -->

<div class="mt-4 d-flex gap-3 flex-wrap">

    <?php if($_SESSION['role'] == "doctor"){ ?>

    <a href="../queue/next_patient.php" class="btn btn-success"> Call Next
    </a>


   <a href="../queue/reset_queue.php" onclick="return confirm('Reset all queue data?')">
   <button class="btn btn-danger"> Reset Queue </button>
    </a>

    <?php } ?>

</div>


<!-- Waiting Queue Table -->

<div class="table-card mt-5">
<h5 class="mb-3">Waiting Queue</h5>

<table class="table table-hover">

<thead>
    <tr>
    <th>Queue Number</th>
    <th>Patient Name</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

    <tbody id="waiting_list"></tbody>

</table>

</div>

<!-- Previous Queue Table -->

<div class="table-card mt-5">
    <h5 class="mb-3">Passed Queue</h5>
    <input type="text" id="previousSearch"
           class="form-control mb-3"
           placeholder="Search previous queue...">

    <table class="table" id="previousTable">

    <table class="table table-hover" id="previousTable">

<thead>
    <tr>
    <th>Queue Number</th>
    <th>Patient Name</th>
    <th>Status</th>
    <th>Action</th>
</tr>

</thead>

    <tbody id="passed_list"></tbody>

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
document.getElementById("total_patients").innerText = data.total;
document.getElementById("next").innerText = data.next;


// update queue table
let queueTable = document.getElementById("waiting_list");

queueTable.innerHTML = "";

data.waiting.forEach(function(queue){

let row = `
<tr>
<td>${queue.queue_number}</td>
<td>${queue.name}</td>

<td>
    <span class="badge bg-warning text-dark">
        Waiting
    </span>
</td>

<td>
    <button class="btn btn-sm btn-primary"
        onclick="callPatient('${queue.queue_number}')">
        Call
    </button>
</td>
</tr>
`;

let previousText = "-"; //passed bar

if(data.previous.length > 0){
    previousText = data.previous[0].queue_number;
}

document.getElementById("previous").innerText = previousText;


queueTable.innerHTML += row;
});

// update passed table

let passedTable = document.getElementById("passed_list");

passedTable.innerHTML = "";

if(data.passed){

    data.passed.forEach(function(queue){

let row = `
<tr>
<td>${queue.queue_number}</td>
<td>${queue.name}</td>

<td>
    <span class="badge bg-success text-light">Passed</span>
</td>

<td>
    <button class="btn btn-sm btn-warning btn-lg" onclick="recallPatient('${queue.queue_number}')">
        Recall
    </button>
</td>
</tr>
`;

passedTable.innerHTML += row;

});
}

})
.catch(error => console.log("Queue error:", error));
}


function callPatient(queueNumber){

    fetch("../queue/call_specific.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "queue_number=" + queueNumber
    })
    .then(res => res.text())
    .then(() => {
        loadQueue(); // refresh instantly
    })
    .catch(err => console.log(err));
}


function recallPatient(queueNumber){

    fetch("../queue/recall_patient.php", {

        method: "POST",

        headers: {
            "Content-Type":
            "application/x-www-form-urlencoded"
        },

        body: "queue_number=" + queueNumber

    })
    .then(res => res.text())
    .then(() => {

        loadQueue();

    })
    .catch(err => console.log(err));
}

// Search Passed Queue Table
document.getElementById("previousSearch")
.addEventListener("keyup", function() {

    let value = this.value.toLowerCase();

    let rows = document.querySelectorAll("#previousTable tbody tr");

    rows.forEach(function(row){

        row.style.display =
            row.innerText.toLowerCase().includes(value)
            ? ""
            : "none";

    });

});


// load immediately
loadQueue();

// refresh every 3 seconds
setInterval(loadQueue,3000);

// prevent back button after logout
history.pushState(null, null, location.href);

window.onpopstate = function () {
    history.go(1);
};

// auto redirect if session expired
window.addEventListener("pageshow", function (event) {

    if (event.persisted) {
        window.location.reload();
    }

});

</script>

</body>
</html>