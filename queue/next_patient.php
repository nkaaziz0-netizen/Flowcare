<?php
include("../config/config.php");
include("../notifications/send_telegram.php");

session_start();

if($_SESSION['role'] != "doctor"){
    header("Location: ../dashboard/dashboard.php");
    exit();
}

// STEP 1: check if there is waiting patient FIRST
$next = $conn->query("
    SELECT * FROM patients 
    WHERE status='waiting' 
    ORDER BY queue_position ASC 
    LIMIT 1
");


if($next->num_rows > 0){

    // ✅ ONLY if there is next → proceed

    // get current serving patient
$current = $conn->query("
    SELECT id 
    FROM patients
    WHERE status='serving'
    LIMIT 1
");

    if($current->num_rows > 0){

        $currentPatient = $current->fetch_assoc();

        $current_id = $currentPatient['id'];

        // log previous patient
    $conn->query("
        INSERT INTO patient_logs (queue_number, action)
        SELECT queue_number, 'served'
        FROM patients
        WHERE id = $current_id
    ");

        // move current patient to passed
    $conn->query("
        UPDATE patients
        SET status='passed'
        WHERE id = $current_id
        ");
    }

        // move next waiting patient to serving
    $nextPatient = $next->fetch_assoc();

    $next_id = $nextPatient['id'];

    $doctor_id = $_SESSION['user_id'];
    $conn->query("
        UPDATE patients
        SET status='serving',
            called_by='$doctor_id'
        WHERE id = $next_id
    ");

    // Notify currently called patient
    $currentPhone = "+6" . ltrim($nextPatient['phone'], "0");

    $currentMessage = "FlowCare: Your queue ".$nextPatient['queue_number']." is now being called. Please proceed to consultation room.";
    sendTelegram($currentPhone, $currentMessage);

    $currentPosition = $nextPatient['queue_position'];
    $targetPosition = $currentPosition + 1;

    $notify = $conn->query("
        SELECT * FROM patients
        WHERE queue_position = '$targetPosition'
        AND status='waiting'
        AND notified = 0
        LIMIT 1
    ");

    if($notify->num_rows > 0){

    $notifyPatient = $notify->fetch_assoc();

    $notifyId = $notifyPatient['id'];

    $queueNumber = $notifyPatient['queue_number'];

    // Convert phone format
    $phone = "+6" . ltrim($notifyPatient['phone'], "0");

    // Telegram message
    $message = "FlowCare: Your queue ".$queueNumber." upcoming. Please prepare to enter consultation room.";

    // Send Telegram
    sendTelegram($phone, $message);

    // Log notification
    $conn->query("
        INSERT INTO patient_logs(queue_number, action)
        VALUES('$queueNumber', 'notification_sent')
    ");

    // Mark notified
    $conn->query("
        UPDATE patients
        SET notified = 1
        WHERE id = '$notifyId'
    ");

    echo "
        <script>
            alert('Notification sent to Queue: $queueNumber');
        </script>
    ";
}}
// ❗ if NO waiting → do NOTHING (keep current serving)

echo "
<script>
window.location='../dashboard/dashboard.php';
</script>
";
?>