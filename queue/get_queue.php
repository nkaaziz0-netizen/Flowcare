<?php
include("../config/config.php");

$response = [];

// current serving patient
$serving_query = $conn->query("
    SELECT queue_number 
    FROM patients 
    WHERE status='serving' 
    ORDER BY created_at ASC 
    LIMIT 1
");

$serving = "-";
if($serving_query->num_rows > 0){
    $row = $serving_query->fetch_assoc();
    $serving = $row['queue_number'];
}

// previous (last done)
$prev = $conn->query("
    SELECT queue_number FROM patients 
    WHERE status='done' 
    ORDER BY created_at DESC
    LIMIT 1
");

if($prev->num_rows > 0){
    $response['previous'] = $prev->fetch_assoc()['queue_number'];
}else{
    $response['previous'] = "-";
}

// next (first waiting)
$nextOne = $conn->query("
    SELECT queue_number FROM patients 
    WHERE status='waiting' 
    ORDER BY created_at ASC 
    LIMIT 1
");

if($nextOne->num_rows > 0){
    $response['next'] = $nextOne->fetch_assoc()['queue_number'];
}else{
    $response['next'] = "-";
}

// waiting queue
$waiting_query = $conn->query("
    SELECT queue_number 
    FROM patients 
    WHERE status='waiting' 
    ORDER BY created_at ASC
");

$waiting = [];
while($row = $waiting_query->fetch_assoc()){
    $waiting[] = $row['queue_number'];
}

// ✅ TOTAL PATIENTS (FIXED)
$total_query = $conn->query("
    SELECT COUNT(*) as total 
    FROM patients 
    WHERE status IN ('waiting','serving')
");

$total = 0;
if($total_query->num_rows > 0){
    $row = $total_query->fetch_assoc();
    $total = $row['total'];
}

// calculate waiting time
$countWaiting = $conn->query("SELECT COUNT(*) as total FROM patients WHERE status='waiting'");
$row = $countWaiting->fetch_assoc();

$patientsAhead = $row['total'];

$estimatedTime = $patientsAhead * 5; // 5 minutes per patient

$response['estimated_wait'] = $estimatedTime;

$response['serving'] = $serving;
$response['waiting'] = $waiting;
$response['total'] = $total;

header('Content-Type: application/json');
echo json_encode($response);
?>

