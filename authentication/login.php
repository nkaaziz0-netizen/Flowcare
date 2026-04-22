<?php
session_start();
include("../config/config.php");

$message="";

if($_SERVER["REQUEST_METHOD"]=="POST"){

$username=$_POST['username'];
$password=$_POST['password'];

$sql = "SELECT * FROM users WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows==1){

$user=$result->fetch_assoc();

$_SESSION['username']=$user['username'];
$_SESSION['role']=$user['role'];
$_SESSION['user_id']=$user['id']; 

//to insert login
$stmt = $conn->prepare("INSERT INTO logs (user_id, username, action) VALUES (?, ?, ?)");
$action = "login";
$stmt->bind_param("iss", $user['id'], $user['username'], $action);
$stmt->execute();

// redirect AFTER logging
header("Location: ../dashboard/dashboard.php");
exit();

}else{

$message="Invalid Login";

}

}
?>

<!DOCTYPE html>
<html>
<head>

<title>FlowCare Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

<style>

body{
height:100vh;
font-family:'Segoe UI', sans-serif;
background:#f3f4f6;
}

.left-panel{
background:#f8fafc;
}

.login-card{
background:white;
padding:40px;
border-radius:20px;
box-shadow:0 15px 35px rgba(0,0,0,0.1);
width:100%;
max-width:420px;
}

.logo{
font-size:34px;
font-weight:700;
color:#14b8a6;
}

.welcome{
font-size:28px;
font-weight:600;
}

.form-control{
border-radius:10px;
padding:12px;
}

.btn-login{
background:#14b8a6;
border:none;
border-radius:10px;
padding:12px;
font-weight:600;
}

.btn-login:hover{
background:#0f766e;
}

.right-panel{
background:linear-gradient(135deg,#0f766e,#14b8a6);
display:flex;
align-items:center;
justify-content:center;
color:white;
}

.content-box{
max-width:450px;
}

.right-title{
font-size:42px;
font-weight:bold;
margin-bottom:20px;
}

.right-desc{
font-size:18px;
opacity:0.9;
}

</style>

</head>

<body>

<div class="container-fluid vh-100">
<div class="row vh-100">

<!-- LEFT LOGIN PANEL -->
<div class="col-md-5 d-flex align-items-center justify-content-center left-panel">

<div class="login-card">

<div class="logo mb-3">FlowCare</div>
<div class="welcome mb-4">Welcome Back 👋</div>

<?php if($message!=""){ ?>
<div class="alert alert-danger"><?php echo $message; ?></div>
<?php } ?>

<form method="POST">

<div class="mb-3">
<label class="form-label">Username</label>
<input type="text" name="username" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<button class="btn btn-login w-100">Sign In</button>

</form>

</div>

</div>


<!-- RIGHT PANEL -->
<div class="col-md-7 right-panel">

<div class="content-box text-center">

<h1 class="right-title">Smart Clinic Queue</h1>

<p class="right-desc">
FlowCare helps clinics manage patient queues efficiently with real-time monitoring,
smart registration and seamless workflow.
</p>

</div>

</div>

</div>
</div>


</body>
</html>