<?php
include("../config/config.php");

// log previous serving
$conn->query("
    INSERT INTO patient_logs (queue_number, action)
    SELECT queue_number, 'served' 
    FROM patients 
    WHERE status='serving'
");

// log manual call
$conn->query("
    INSERT INTO patient_logs (queue_number, action)
    VALUES ('$queue', 'manual_call')
");

if(isset($_POST['queue_number'])){

    $queue = $_POST['queue_number'];

    // 1. Move current serving to done
    $conn->query("
        UPDATE patients 
        SET status='done' 
        WHERE status='serving'
    ");

    // 2. Set selected patient to serving
    $conn->query("
        UPDATE patients 
        SET status='serving' 
        WHERE queue_number='$queue'
    ");
}
?>