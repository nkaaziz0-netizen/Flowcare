<?php
include("../config/config.php");

$response = [];

// current serving patient
$serving_query = $conn->query("
    SELECT patients.queue_number, patients.nickname, users.location
    FROM patients
    LEFT JOIN users ON patients.called_by = users.id
    WHERE patients.status='serving'
    ORDER BY patients.created_at ASC
    LIMIT 1
");

$serving = "-";
$location = "-";

if($serving_query->num_rows > 0){

    $row = $serving_query->fetch_assoc();

    $serving = $row['queue_number'];
    $location = $row['location'];
}

// previous (last passed)
$prev_query = $conn->query("
    SELECT patients.queue_number, patients.nickname, users.location
    FROM patients
    LEFT JOIN users ON patients.called_by = users.id
    WHERE patients.status='passed'
    ORDER BY patients.id DESC
    LIMIT 4
");

$previous = [];

while($row = $prev_query->fetch_assoc()){

    $previous[] = [
        "queue_number" => $row['queue_number'],
        "nickname" => $row['nickname'],
        "location" => $row['location']
    ];
}

// next (first waiting)
$nextOne = $conn->query("
    SELECT queue_number, nickname
    FROM patients 
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
    SELECT queue_number, name, nickname
    FROM patients 
    WHERE status='waiting' 
    ORDER BY created_at ASC
");

$waiting = [];

while($row = $waiting_query->fetch_assoc()){

    $waiting[] = [
        "queue_number" => $row['queue_number'],
        "name"=> $row['name'],
        "nickname" => $row['nickname']
    ];
}

//passed queue
$passed_query = $conn->query("
    SELECT queue_number, name
    FROM patients
    WHERE status='passed'
    ORDER BY queue_position ASC
");

$passed = [];
while($row = $passed_query->fetch_assoc()){
    $passed[] = [
        "queue_number" => $row['queue_number'],
        "name" => $row['name']
    ];
}

// TOTAL PATIENTS (FIXED)
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
$response['location'] = $location;
$response['passed'] = $passed;
$response['waiting'] = $waiting;
$response['total'] = $total;
$response['previous'] = $previous;

header('Content-Type: application/json');
echo json_encode($response);
?>

