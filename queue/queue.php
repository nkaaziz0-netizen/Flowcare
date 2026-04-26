
<?php
include("../config/config.php");

// Current serving patient
$current = $conn->query("SELECT queue_number FROM patients 
WHERE status='serving' ORDER BY created_at ASC LIMIT 1");

// Next waiting patients
$next = $conn->query("SELECT queue_number FROM patients 
WHERE status='waiting' ORDER BY created_at ASC LIMIT 5");

$current_queue = "None";
if($current->num_rows > 0){
    $row = $current->fetch_assoc();
    $current_queue = $row['queue_number'];
}
?>

<!DOCTYPE html>
<html>
<head>

<title>FlowCare Queue Display</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

<style>

body{
    background: #9ecae1;;
    color:black;
    font-family:Arial;
}

.title{
    font-size:48px;
    font-weight:bold;
    letter-spacing:2px;
}

/* LEFT PANEL */
.serving-box{
    background:white;
    color:black;
    border-radius:20px;
    padding:30px;
}

/* BIG NUMBER */
.serving-number{
    font-size:140px;
    font-weight:bold;
    color:red;
    animation: pulse 1.5s infinite;
}

/* RIGHT PANEL */
.queue-box{
    background:white;
    color:black;
    border-radius:20px;
    padding:30px;
}

/* NEXT ITEMS */
.waiting-card{
    background:#9ecae1;
    border-radius:12px;
    padding:15px;
    font-size:28px;
    font-weight:bold;
}

.previous-number{
    font-size: 60px;
    font-weight: bold;
    color: blue;
}

/* animation */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>

</head>

<body>

<div class="container-fluid mt-4">

    <div class="text-center title mb-4">
        FLOWCARE CLINIC QUEUE
    </div>

    <div class="row">

        <!-- LEFT: NOW SERVING -->
        <div class="col-md-8">
            <div class="serving-box text-center h-100">

                <div class="d-flex justify-content-center align-items-center gap-2">
                    <h4 class="mb-0">NOW SERVING</h4>
                    <span class="badge bg-success">LIVE</span>
                </div>

                <div id="serving" class="serving-number mt-3">-</div>

                    <div class="mt-2">
                        <small class="text-muted">Previous</small>
                        <div id="previousQueue" class="previous-number">-</div>
                    </div>

                <h4 id="serving_name" class="mt-2"></h4>

                <h5 id="estimated_time" class="mt-2"></h5>

                <h3 id="clock" class="mt-3 text-muted"></h3>

            </div>
        </div>

        <!-- RIGHT: NEXT QUEUE -->
        <div class="col-md-4">
            <div class="queue-box text-center h-100">

                <h4 class="mb-4">UPCOMING</h4>

                <div id="waiting_list" class="d-flex flex-column gap-3"></div>

            </div>
        </div>

    </div>


<script>

function loadQueue(){

fetch("get_queue.php")
.then(res => res.json())
.then(data => {

console.log(data); // helps debug

// update current serving
document.getElementById("serving").innerText = data.serving;
document.getElementById("serving_name").innerText = data.serving_name;
document.getElementById("previousQueue").innerText = data.previous;

// update estimated time
document.getElementById("estimated_time").innerText =
"Estimated Wait: " + data.estimated_wait + " minutes";

// update waiting list
let list = document.getElementById("waiting_list");
list.innerHTML = "";

data.waiting.forEach(function(queue){

let div = document.createElement("div");
div.className = "waiting-card text-center";

div.innerText = queue;

list.appendChild(div);

});

})
.catch(error => console.log("Queue error:", error));

}

// load immediately
loadQueue();

// refresh every 3 seconds
setInterval(loadQueue,3000);

// clock
setInterval(function(){
const now = new Date();
document.getElementById("clock").innerText = now.toLocaleTimeString();
},1000);

</script>

</body>
</html>

