<?php
include("../config/config.php");

session_start();

if($_SESSION['role'] != "doctor"){
    header("Location: ../dashboard/dashboard.php");
    exit();
}

// reset queue
$conn->query("DELETE FROM patients");

// optional: reset AUTO_INCREMENT
$conn->query("ALTER TABLE patients AUTO_INCREMENT = 1");

header("Location: ../dashboard/dashboard.php");
exit();
?>