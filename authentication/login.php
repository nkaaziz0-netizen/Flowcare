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

/* VERIFY PASSWORD */
if(
    password_verify($password, $user['password']) ||
    $password === $user['password']
){

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

$message="Wrong Password";

}

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
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    display:flex;
    min-height:100vh;
    font-family:'Segoe UI', sans-serif;
    background:#f3f4f6;
}

/* LEFT SIDE */
.left-panel{
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px;
    background:#f8fafc;
}

/* LOGIN CARD */
.login-card{
    background:white;
    padding:40px;
    border-radius:20px;
    box-shadow:0 15px 35px rgba(0,0,0,0.1);
    width:100%;
    max-width:420px;
}

/* LOGO */
.logo{
    font-size:34px;
    font-weight:700;
    color:#14b8a6;
    margin-bottom:10px;
}

.welcome{
    font-size:28px;
    font-weight:600;
    margin-bottom:30px;
}

/* INPUT */
.form-control{
    border-radius:10px;
    padding:12px;
    margin-bottom:20px;
}

/* BUTTON */
.btn-login{
    background:#14b8a6;
    border:none;
    border-radius:10px;
    padding:12px;
    font-weight:600;
    width:100%;
    color:white;
    transition:0.3s;
}

.btn-login:hover{
    background:#0f766e;
}

/* RIGHT SIDE */
.right-panel{
    flex:1;
    background:url('/assets/img/bg.jpg') center center/cover no-repeat;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:40px;
    text-align:center;
}


/* CONTENT */
.content-box{
    width:100%;
    max-width:500px;
}

.right-title{
    font-size:48px;
    font-weight:bold;
    margin-bottom:20px;
}

.right-desc{
    font-size:20px;
    opacity:0.9;
    line-height:1.6;
}

/* ========================= */
/* RESPONSIVE */
/* ========================= */

@media(max-width:992px){

    body{
        flex-direction:column;
    }

    .left-panel,
    .right-panel{
        width:100%;
        min-height:50vh;
    }

    .right-title{
        font-size:36px;
    }

    .right-desc{
        font-size:18px;
    }
}

.logo-img{
    max-width:100%;
    height:auto;
    display:block;
    margin:0 auto 20px;
    object-fit:contain;
}

@media(max-width:576px){

    .login-card{
        padding:25px;
        border-radius:15px;
        overflow:hidden;
    }

    .logo{
        font-size:28px;
    }

    .welcome{
        font-size:24px;
    }

    .right-title{
        font-size:28px;
    }

    .right-desc{
        font-size:16px;
    }

    .left-panel,
    .right-panel{
        padding:20px;
    }

    
}

</style>

</head>

<body>

<div class="container-fluid vh-100">
<div class="row vh-100">

<!-- LEFT LOGIN PANEL -->
<div class="col-md-5 d-flex align-items-center justify-content-center left-panel">

<div class="login-card">

<img src="../assets/img/Fl2.png" class="logo-img">
<div class="welcome mb-4">Welcome Back 👋</div>

<?php if(isset($_GET['error'])){ ?>
    <div class="alert alert-warning text-center">
<?php

if($_GET['error'] == "loginfirst"){
    echo "Session expired. Please login again.";
}
?>
</div>
<?php } ?>

<?php 
    if($message!=""){ ?>
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

<h1 class="right-title">Self-Queue Clinic</h1>

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