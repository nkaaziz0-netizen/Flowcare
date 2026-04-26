<?php
include("../config/config.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nric   = $_POST['nric'];
    $name   = $_POST['name'];
    $phone  = $_POST['phone'];
    $gender = $_POST['gender'];

    // Generate random queue number (example: A123)
    $result = $conn->query("SELECT MAX(id) as max_id FROM patients");
    $row = $result->fetch_assoc();

    $next = $row['max_id'] + 1;
    $queueNumber = "A" . str_pad($next, 3, "0", STR_PAD_LEFT);

    $sql = "INSERT INTO patients (nric, name, phone, gender, queue_number, status)
            VALUES ('$nric', '$name', '$phone', '$gender', '$randomNumber', 'waiting')";

    if ($conn->query($sql) === TRUE) {
        $message = "Your Queue Number is: " . $randomNumber;
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - FlowCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Patient Registration</h2>

    <?php if($message != "") { ?>
        <div class="alert alert-success">
            <h4><?php echo $message; ?></h4>
            <a href="../authentication/register.php" class="btn btn-primary mt-3">Register Another</a>
        </div>
    <?php } else { ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">NRIC</label>
            <input type="text" name="nric" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-control">
                <option>Male</option>
                <option>Female</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="../public/index.html" class="btn btn-secondary">Back</a>
    </form>

    <?php } ?>
</div>

</body>
</html>

