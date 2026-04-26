<?php
include("../config/config.php");

$response = [];

// current serving patient
$current = $conn->query("SELECT queue_number,name FROM patients 
WHERE status='serving' LIMIT 1");

if($current->num_rows > 0){
$row = $current->fetch_assoc();
$response['serving'] = $row['queue_number'];
$response['serving_name'] = $row['name'];
}else{
$response['serving'] = "-";
$response['serving_name'] = "";
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
$waiting = $conn->query("SELECT queue_number FROM patients 
WHERE status='waiting' ORDER BY created_at ASC LIMIT 5");

$list = [];

while($row = $waiting->fetch_assoc()){
$list[] = $row['queue_number'];
}

$response['waiting'] = $list;

// calculate waiting time
$countWaiting = $conn->query("SELECT COUNT(*) as total FROM patients WHERE status='waiting'");
$row = $countWaiting->fetch_assoc();

$patientsAhead = $row['total'];

$estimatedTime = $patientsAhead * 5; // 5 minutes per patient

$response['estimated_wait'] = $estimatedTime;

header('Content-Type: application/json');

echo json_encode($response);
?>

