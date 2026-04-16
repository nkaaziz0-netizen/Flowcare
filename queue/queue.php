
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
background:#111;
color:white;
font-family:Arial;
}

.title{
font-size:50px;
font-weight:bold;
}

.serving-box{
background:white;
color:black;
border-radius:20px;
padding:40px;
}

.serving-number{
font-size:120px;
font-weight:bold;
color:red;
}

.waiting-title{
font-size:35px;
margin-top:40px;
}

.waiting-number{
font-size:45px;
font-weight:bold;
}

</style>

</head>

<body>

<div class="container text-center mt-5">

<div class="title">FLOWCARE CLINIC QUEUE</div>

<br><br>

<div class="serving-box">

<h2>NOW SERVING</h2>

<div id="serving" class="serving-number">-</div>

<h2 id="serving_name"></h2>

<h4 id="estimated_time" class="mt-3"></h4>

<h3 id="clock"></h3>

</div>

<div class="waiting-title">NEXT QUEUE</div>

<div id="waiting_list" class="row justify-content-center mt-3"></div>

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

// update estimated time
document.getElementById("estimated_time").innerText =
"Estimated Wait: " + data.estimated_wait + " minutes";

// update waiting list
let list = document.getElementById("waiting_list");
list.innerHTML = "";

data.waiting.forEach(function(queue){

let div = document.createElement("div");
div.className = "col-md-2 waiting-number";

div.innerHTML = `
<div class="card p-3 text-dark text-center">
${queue}
</div>
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

