<?php

// Detect environment (Railway vs local)
$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'root';
$db   = getenv('DB_NAME') ?: 'flowcare';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>