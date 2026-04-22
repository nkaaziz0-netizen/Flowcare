<?php
session_start();
include("../config/config.php");

// 🔒 Protect page (ADMIN ONLY)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../authentication/login.php");
    exit();
}

$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();

    $success = true;

}
?>

<<!DOCTYPE html>
<html>
<head>
<title>Create User</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
body{
    background:#f5f7fa;
}

.card{
    border:none;
    border-radius:16px;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
}

.btn-primary{
    border-radius:10px;
    padding:10px;
}

</style>

</head>

<body>

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">

<div class="card p-4" style="width:400px;">

<h4 class="mb-4 text-center">
<i class="bi bi-person-plus"></i> Create User
</h4>

<?php if($success): ?>
<div class="alert alert-success text-center">
    <i class="bi bi-check-circle"></i> User created successfully!
</div>
<?php endif; ?>

<form method="POST">

<div class="mb-3">
<label class="form-label">Username</label>
<input type="text" name="username" class="form-control" placeholder="Enter username" required>
</div>

<div class="mb-3">
<label class="form-label">Password</label>
<input type="password" name="password" class="form-control" placeholder="Enter password" required>
</div>

<div class="mb-3">
<label class="form-label">Role</label>
<select name="role" class="form-select">
<option value="doctor">Doctor</option>
<option value="admin">Admin</option>
</select>
</div>

<button type="submit" class="btn btn-primary w-100">
<i class="bi bi-check-circle"></i> Create User
</button>

</form>

<a href="../dashboard/dashboard.php" class="btn btn-light mt-3 w-100">
<i class="bi bi-arrow-left"></i> Back to Dashboard
</a>

</div>

</div>

</body>
</html>