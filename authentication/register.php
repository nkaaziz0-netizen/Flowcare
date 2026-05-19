<?php
include("../config/config.php");

$message = "";
$error = "";

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

    // Queue Position
    $getLastPosition = $conn->query("
    SELECT MAX(queue_position) as last_pos 
    FROM patients
    ");

    // validate dummy NRIC format
    if(!preg_match("/^\d{6}-\d{2}-\d{4}$/", $nric)){
        $error = "Please enter valid IC number";
    }

    else{
    // check nric exists in registered_patients
        $check_nric = $conn->query("
        SELECT * FROM registered_patients
        WHERE nric='$nric'
        ");

    // if not found
    if($check_nric->num_rows == 0){
         $error = "IC number not registered";
    }    

    // if found
    else {$patient = $check_nric->fetch_assoc();
    $name = $patient['name'];
    }

    }

     // Queue Position
    $getLastPosition = $conn->query("
    SELECT MAX(queue_position) as last_pos 
    FROM patients
    ");

    // Get latest queue position
    $rowPos = $getLastPosition->fetch_assoc();
    $queuePosition = $rowPos['last_pos'] + 1;

    // Insert patient data
    if(empty($error)){
    $stmt = $conn->prepare("
        INSERT INTO patients 
        (nric, name, phone, gender, queue_number, status, queue_position)
        VALUES (?, ?, ?, ?, ?, 'waiting', ?) ");

    $stmt->bind_param(
    "sssssi",
    $nric,
    $name,
    $phone,
    $gender,
    $queueNumber,
    $queuePosition
    );

    if($stmt->execute()){
        $message = "Your Queue Number is: " . $queueNumber;
    } else {
        $message = "Error: " . $conn->error;
    }

}}
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
        <a href="../authentication/register.php" class="btn btn-primary mt-3">
            Register Another
        </a>
    </div>
    <?php } ?>

    <?php if(!empty($error)) : ?>
    <div class="alert alert-danger text-center">
        <?php echo $error; ?>
    </div>
    <?php endif; ?>


    <form method="POST">
        <div class="mb-3">
            <label class="form-label">NRIC</label>
            <input type="text" name="nric" id="nric" class="form-control" maxlength="14" placeholder="0000XX-XX-XXXX" required>
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

</div>

</body>

<script>

document.getElementById("nric").addEventListener("input", function(e){

    // remove non numeric
    let value = this.value.replace(/\D/g, '');

    // limit 12 digits only
    value = value.substring(0,12);

    // auto insert dash
    if(value.length > 6 && value.length <= 8){
        value = value.replace(/(\d{6})(\d+)/, '$1-$2');
    }
    else if(value.length > 8){
        value = value.replace(/(\d{6})(\d{2})(\d+)/, '$1-$2-$3');
    }

    this.value = value;

});

</script>

</html>

