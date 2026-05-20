
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
    background:#f7f5f0;
    color:#2d3748;
    font-family:Arial, sans-serif;
    overflow-x:hidden;
}

/* TITLE */

.title{
    font-size:clamp(30px,5vw,70px);
    font-weight:800;
    letter-spacing:2px;
}

/* MAIN PANELS */

.serving-box,
.queue-box{
    background:white;
    color:black;
    border-radius:30px;
    padding:25px;
    height:100%;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

/* NOW SERVING */

.serving-number{
    font-size:clamp(70px,12vw,180px);
    font-weight:600;
    color:red;
    animation:pulse 1.5s infinite;
    line-height:1;
}

/* PANELS TITLE */

.panel-title{
    font-size:clamp(20px,2vw,35px);
    font-weight:600;
}

/* QUEUE CARD */

.waiting-card{
    background:#e2eff0;
    border-radius:20px;
    padding:18px;
    margin-bottom:15px;
    text-align:center;
    transition:0.3s;
}

.waiting-card:hover{
    transform:translateY(-3px);
}

/* QUEUE NUMBER */

.waiting-card .number{
    font-size:clamp(28px,4vw,55px);
    font-weight:600;
}

.waiting-card .room{
    font-size:16px;
    color:#555;
    margin-top:5px;
    font-weight:500;
}

/* CLOCK */

#clock{
    font-size:clamp(25px,3vw,50px);
}

/* ESTIMATED WAIT */

#estimated_time{
    font-size:clamp(18px,2vw,28px);
}

/* LIVE BADGE */

.badge{
    font-size:14px;
    padding:8px 14px;
    border-radius:20px;
}

.logo-wrapper{
    text-align:center;
    margin-bottom:0;
    padding-bottom:0;
}

.queue-logo{
    text-align:center;
    width:40%;
    height:auto;
    display:inline-block;
    margin:0;
}

/* ANIMATION */

@keyframes pulse {

    0%{
        transform:scale(1);
    }

    50%{
        transform:scale(1.03);
    }

    100%{
        transform:scale(1);
    }

}


/* MOBILE */

@media(max-width:768px){

    .waiting-card{
        padding:10px;
        border-radius:12px;
    }

    .waiting-card .number{
        font-size:28px;
    }

    .waiting-card .name{
        font-size:12px;
    }

    .serving-number{
        font-size:55px;
    }

    #clock{
        font-size:18px;
    }

}

</style>

</head>

<body>

    <div class="logo-wrapper">
        <img src="../assets/img/Fl2.png"
            alt="FlowCare"
            class="queue-logo">
    </div>

    <div class="row g-4 mt-0">

        <!-- LEFT: Previous -->
        <div class="col-lg-3 col-md-4 col-4">
            <div class="queue-box text-center h-100">

            <h4 class="panel-tittle mb-4">PREVIOUS</h4>

            <div id="previous_queue" class="queue-list"></div>

            </div>
        </div>

        <!-- Center: Now Serving -->
        <div class="col-lg-6 col-md-4 col-4">
            <div class="serving-box text-center h-100">

                <div class="d-flex justify-content-center align-items-center gap-2">
                    <h4 class="mb-0">NOW SERVING</h4>
                    <span class="badge bg-success">LIVE</span>
                </div>

                <div id="serving" class="serving-number mt-3">-</div>

                <h3 id="location" class="mt-3 text-primary fw-bold"></h3>

                <h5 id="estimated_time" class="mt-2"></h5>

                <h3 id="clock" class="mt-3 text-muted"></h3>

            </div>
        </div>

        <!-- RIGHT: NEXT QUEUE -->
        <div class="col-lg-3 col-md-4 col-4">
                <div class="queue-box text-center h-100">

                <h4 class="mb-4">UPCOMING</h4>

                <div id="waiting_list" class="queue-list"></div>

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

// update current location
document.getElementById("location").innerText = data.location;

// update estimated time
document.getElementById("estimated_time").innerText = "Estimated Wait: " + data.estimated_wait + " minutes";

// update previous list
let previous = document.getElementById("previous_queue");
previous.innerHTML = "";

data.previous.slice(0,4).forEach(function(queue){

    let div = document.createElement("div");

    div.className = "waiting-card";

    div.innerHTML = `
        <div class="number">${queue.queue_number}</div>
        <div class="room">${queue.location}</div>
    `;

    previous.appendChild(div);

});

// update waiting list
let list = document.getElementById("waiting_list");

list.innerHTML = "";

data.waiting.slice(0,4).forEach(function(queue){

    let div = document.createElement("div");

    div.className = "waiting-card";

    div.innerHTML = `
        <div class="number">${queue.queue_number}</div>
    `;
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

