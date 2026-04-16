<?php

$host = "db";        // IMPORTANT: use 'db' not localhost (because Docker)
$user = "root";
$pass = "root";
$db   = "flowcare";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
