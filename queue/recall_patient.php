<?php
include("../config/config.php");

// get last done patient
$prev = $conn->query("
    SELECT id FROM patients 
    WHERE status='done' 
    ORDER BY created_at DESC 
    LIMIT 1
");

if($prev->num_rows > 0){

    $row = $prev->fetch_assoc();
    $id = $row['id'];

    // set current serving to done first
    $conn->query("
        UPDATE patients 
        SET status='done' 
        WHERE status='serving'
    ");

    // recall previous patient
    $conn->query("
        UPDATE patients 
        SET status='serving' 
        WHERE id=$id
    ");

    $conn->query("
    INSERT INTO patient_logs (queue_number, action)
    VALUES (
        (SELECT queue_number FROM patients WHERE id=$id),
        'recalled'
    )
");
}

header("Location: ../dashboard/dashboard.php");
exit();