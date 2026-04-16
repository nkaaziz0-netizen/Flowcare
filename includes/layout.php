<!DOCTYPE html>
<html>
<head>

<title>FlowCare</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/assets/css/style.css">

<!-- ICONS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<!-- TOPBAR -->

<div class="topbar">

<button class="menu-btn" onclick="toggleSidebar()">
<i class="fa fa-bars"></i>
</button>

<div class="logo">FlowCare</div>

</div>

<!-- SIDEBAR -->

<div class="sidebar" id="sidebar">

<a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a> 

<a href="next_patient.php"><i class="fa fa-user-md"></i> Call Next</a> 

<a href="queue.php"><i class="fa fa-tv"></i> Queue Display</a> 

<a href="register.php"><i class="fa fa-user-plus"></i> Register Patient</a> 

<a href="patient_dashboard.php"><i class="fa fa-user"></i> Patient Panel</a> 

<a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a> 

</div>

<div class="overlay" id="overlay" onclick="toggleSidebar()"></div>