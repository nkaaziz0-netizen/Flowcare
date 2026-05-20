<?php
session_start();
include("../config/config.php");

/* PREVENT BACK CACHE */
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

/* CHECK LOGIN */
if(!isset($_SESSION['user_id'])){
    header("Location: ../authentication/login.php");
    exit();
}


$id = $_GET['id'];

/* Get patient data */
$patient = $conn->query("SELECT * FROM patients WHERE id='$id'");
$row = $patient->fetch_assoc();

/* Check profile exists */
$profile = $conn->query("SELECT * FROM patient_profiles WHERE patient_id='$id'");

$profileData = $profile->fetch_assoc();

$success = "";
/* Save medical profile */
if(isset($_POST['save'])){

    $blood_type = $_POST['blood_type'];
    $allergies = $_POST['allergies'];
    $chronic_disease = $_POST['chronic_disease'];
    $current_medication = $_POST['current_medication'];
    $previous_surgery = $_POST['previous_surgery'];
    $medical_notes = $_POST['medical_notes'];

    if($profileData){

        $conn->query("
            UPDATE patient_profiles SET
            blood_type='$blood_type',
            allergies='$allergies',
            chronic_disease='$chronic_disease',
            current_medication='$current_medication',
            previous_surgery='$previous_surgery',
            medical_notes='$medical_notes'
            WHERE patient_id='$id'
        ");

    } else {

        $conn->query("
            INSERT INTO patient_profiles
            (patient_id, blood_type, allergies, chronic_disease, current_medication, previous_surgery, medical_notes)

            VALUES
            ('$id','$blood_type','$allergies','$chronic_disease','$current_medication','$previous_surgery','$medical_notes')
        ");
    }

    $success = "Medical profile saved successfully!";}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Patient Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<?php if($success != "") { ?>

<div class="alert alert-success alert-dismissible fade show">

    <?= $success ?>

    <button type="button"
    class="btn-close"
    data-bs-dismiss="alert"></button>

</div>

<?php } ?>

</head>

<body>

<div class="container mt-4">

    <h2 class="mb-4">Patient Medical Profile</h2>

    <!-- BASIC INFO -->

    <div class="card mb-4">

        <div class="card-header bg-dark text-white">
            Basic Information
        </div>

        <div class="card-body">

            <p><strong>Queue:</strong> <?= $row['queue_number'] ?></p>
            <p><strong>Name:</strong> <?= $row['name'] ?></p>
            <p><strong>NRIC:</strong> <?= $row['nric'] ?></p>
            <p><strong>Phone:</strong> <?= $row['phone'] ?></p>
            <p><strong>Gender:</strong> <?= $row['gender'] ?></p>

        </div>

    </div>

    <!-- MEDICAL FORM -->

    <div class="card">

        <div class="card-header bg-primary text-white">
            Medical Information
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label>Blood Type</label>

                    <input type="text"
                    name="blood_type"
                    class="form-control"

                    value="<?= $profileData['blood_type'] ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label>Allergies</label>

                    <textarea name="allergies"
                    class="form-control"><?= $profileData['allergies'] ?? '' ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Chronic Disease</label>

                    <textarea name="chronic_disease"
                    class="form-control"><?= $profileData['chronic_disease'] ?? '' ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Current Medication</label>

                    <textarea name="current_medication"
                    class="form-control"><?= $profileData['current_medication'] ?? '' ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Previous Surgery</label>

                    <textarea name="previous_surgery"
                    class="form-control"><?= $profileData['previous_surgery'] ?? '' ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Medical Notes</label>

                    <textarea name="medical_notes"
                    class="form-control"><?= $profileData['medical_notes'] ?? '' ?></textarea>
                </div>

                <button type="submit" name="save" class="btn btn-success">
                    Save Medical Profile
                </button>

            </form>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>