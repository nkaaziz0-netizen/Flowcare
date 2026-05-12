<?php
include("../config/config.php");

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

    // get current serving
    $current = $conn->query("
        SELECT * FROM patients 
        WHERE status='serving' 
        LIMIT 1
    ");

    if($current->num_rows > 0){
    $row = $current->fetch_assoc();
    $current_id = $row['id'];

    // ✅ LOG patient before marking done
    $conn->query("
        INSERT INTO patient_logs (queue_number, action)
        SELECT queue_number, 'served'
        FROM patients
        WHERE id = $current_id
    ");

    // move to done
    $conn->query("
        UPDATE patients 
        SET status='done' 
        WHERE id = $current_id
    ");
}
    // move next to serving
    $nextPatient = $next->fetch_assoc();
    $next_id = $row['id'];

    $conn->query("
        UPDATE patients 
        SET status='serving' 
        WHERE id = $next_id
    ");

    $currentPosition = $nextPatient['queue_position'];
    $targetPosition = $currentPosition + 2;

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

        $conn->query("
            INSERT INTO patient_logs(queue_number, action)
            VALUES('$queueNumber', 'notification_sent')
        ");

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
window.location='../dashboard/dashboard.php
</script>
";
?>