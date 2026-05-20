<?php
session_start();

include("../config/config.php");

if(isset($_POST['queue_number'])){

    $queue = $_POST['queue_number'];

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

    // move current serving to done
    $conn->query("
        UPDATE patients 
        SET status='done' 
        WHERE status='serving'
    ");

    // set selected patient to serving
    $doctor_id = $_SESSION['user_id'];

    $conn->query("
        UPDATE patients
        SET status='serving',
            called_by='$doctor_id'
        WHERE queue_number='$queue'
    ");
}
?>