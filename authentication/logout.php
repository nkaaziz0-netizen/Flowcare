<?php
session_start();
include("../config/config.php");

// ✅ Log logout BEFORE destroying session
if(isset($_SESSION['user_id'])){

    $stmt = $conn->prepare("INSERT INTO logs (user_id, username, action) VALUES (?, ?, ?)");
    $action = "logout";
    $stmt->bind_param("iss", $_SESSION['user_id'], $_SESSION['username'], $action);
    $stmt->execute();
}

// destroy session
session_unset();
session_destroy();

// redirect back to login
header("Location: ../authentication/login.php");
exit();
?>