<?php
include("../config/config.php");

$result = $conn->query("SELECT * FROM patients ORDER BY id DESC");

$total = $conn->query("SELECT COUNT(*) as total FROM patients")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>

    <title>Patients List</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

        body{
            background:#f4f7fc;
            font-family:Arial;
        }

        .page-title{
            font-weight:bold;
            color:#1c1c1c;
        }

        .patient-name{
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-box{
            border:none;
            border-radius:15px;
            box-shadow:0 2px 10px rgba(0,0,0,0.05);
        }

        .stats-card{
            background:white;
            padding:20px;
            border-radius:15px;
            box-shadow:0 2px 10px rgba(0,0,0,0.05);
        }

        .stats-icon{
            font-size:30px;
            color:#0d6efd;
        }

        .table{
            border-radius:15px;
            overflow:hidden;
        }

        .table thead{
            background:#1f2937;
            color:white;
        }

        .btn-view{
            border-radius:10px;
            padding:6px 14px;
        }

        .search-box{
            border-radius:12px;
            padding:10px;
        }

    </style>

</head>

<body>

<div class="container py-4">

    <!-- HEADER -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="page-title">Patients Management</h1>
            <p class="text-muted">Manage patient records and medical profiles</p>
        </div>

        <a href="/dashboard/dashboard.php" class="btn btn-outline-dark">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>

    </div>

    <!-- STATS -->

    <div class="row mb-4">

        <div class="col-md-4">

            <div class="stats-card">

                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h6 class="text-muted">Total Patients</h6>
                        <h2><?= $total ?></h2>
                    </div>

                    <i class="bi bi-people-fill stats-icon"></i>

                </div>

            </div>

        </div>

    </div>

    <!-- TABLE CARD -->

    <div class="card card-box">

        <div class="card-body">

            <!-- SEARCH -->

            <div class="row mb-3">

                <div class="col-md-4">

                    <input type="text"
                    id="searchInput"
                    class="form-control search-box"
                    placeholder="Search patient...">

                </div>

            </div>

            <!-- TABLE -->

            <div class="table-responsive">

                <table class="table align-middle" id="patientTable">

                    <thead>

                        <tr>
                            <th>Queue</th>
                            <th>Name</th>
                            <th>NRIC</th>
                            <th>Phone</th>
                            <th width="120">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php while($row = $result->fetch_assoc()) { ?>

                        <tr>

                            <td>
                                <span class="badge bg-primary fs-6">
                                    <?= $row['queue_number'] ?>
                                </span>
                            </td>

                            <td>
                                <span class="patient-name">
                                    <?= $row['name'] ?>
                                </span>
                            </td>

                            <td><?= $row['nric'] ?></td>

                            <td><?= $row['phone'] ?></td>

                            <td>

                                <a href="view_patient.php?id=<?= $row['id'] ?>"
                                class="btn btn-primary btn-sm btn-view">  View
                                </a>

                            </td>

                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<!-- SEARCH SCRIPT -->

<script>

const searchInput = document.getElementById("searchInput");

searchInput.addEventListener("keyup", function(){

    let filter = searchInput.value.toLowerCase();

    let rows = document.querySelectorAll("#patientTable tbody tr");

    rows.forEach(row => {

        let text = row.innerText.toLowerCase();

        row.style.display = text.includes(filter) ? "" : "none";

    });

});

</script>

</body>
</html>