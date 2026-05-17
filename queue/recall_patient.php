<?php
include("../config/config.php");
include("../notifications/send_telegram.php");

if(isset($_POST['queue_number'])){

    $queueNumber = $_POST['queue_number'];

    $conn->query("
        UPDATE patients
        SET status='passed'
        WHERE status='serving'
    ");

    // set selected patient as serving
    $conn->query("
        UPDATE patients
        SET status='serving'
        WHERE queue_number='$queueNumber'
    ");

    // get selected patient
    $patientQuery = $conn->query("
        SELECT * FROM patients
        WHERE queue_number='$queueNumber'
        LIMIT 1
    ");

}


if($patientQuery->num_rows > 0){

    $patient = $patientQuery->fetch_assoc();
    $phone = "+6" . ltrim($patient['phone'], "0");

    $message = "FlowCare Queue Recall Queue Number ".$patient['queue_number']." is being recalled. Please proceed immediately to the consultation room.";

    sendTelegram($phone, $message);

    $conn->query("
        INSERT INTO patient_logs (queue_number, action)
        VALUES ('$queueNumber', 'recalled')");
}

header("Location: ../dashboard/dashboard.php");
exit();

?>

