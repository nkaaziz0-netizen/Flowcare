<?php
include("../config/config.php");

session_start();

if($_SESSION['role'] != "doctor"){
header("Location: ../dashboard/dashboard.php");
exit();
}

// mark current serving patient as done
$conn->query("UPDATE patients SET status='done' WHERE status='serving'");

// get next waiting patient
$result=$conn->query("SELECT * FROM patients WHERE status='waiting' ORDER BY created_at ASC LIMIT 1");

if($result->num_rows>0){

$row=$result->fetch_assoc();

$id=$row['id'];

$conn->query("UPDATE patients SET status='serving' WHERE id=$id");

}

header("Location: ../dashboard/dashboard.php");
?>

<link rel="stylesheet" href="assets/css/style.css">
