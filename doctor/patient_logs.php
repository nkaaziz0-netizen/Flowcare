<?php
include("../config/config.php");

$logs = $conn->query("
    SELECT * FROM patient_logs 
    ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Patient Logs</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>

body{
    background:#f4f7fb;
    font-family:'Inter', sans-serif;
    padding:25px;
    color:#1e293b;
}

/* HEADER */

.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.page-title h1{
    font-size:32px;
    font-weight:700;
    color:#0f172a;
    margin-bottom:5px;
}

.page-title p{
    color:#64748b;
    font-size:14px;
    margin:0;
}

/* TOP BUTTONS */

.top-actions{
    display:flex;
    gap:12px;
}

/* BUTTON STYLE */

.custom-btn{
    background:white;
    border:1px solid #e2e8f0;
    padding:10px 18px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.03);
    font-weight:500;
    text-decoration:none;
    color:#334155;
    transition:0.2s;
    font-size:14px;
}

.custom-btn:hover{
    background:#eff6ff;
    color:#2563eb;
}

/* TABLE */

.table-wrapper{
    background:white;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 8px 24px rgba(15,23,42,0.05);
    border:1px solid #e2e8f0;
}

.custom-table{
    margin-bottom:0;
}

.custom-table thead{
    background:#f8fafc;
}

.custom-table thead th{
    padding:18px 22px;
    font-size:15px;
    font-weight:700;
    color:#0f172a;
    border-bottom:1px solid #e2e8f0;
}

.custom-table tbody td{
    padding:18px 22px;
    vertical-align:middle;
    border-bottom:1px solid #edf2f7;
    font-size:14px;
    color:#334155;
}

/* QUEUE TAG */

.queue-tag{
    background:#eaf2ff;
    color:#2563eb;
    padding:8px 14px;
    border-radius:10px;
    font-weight:600;
    display:inline-block;
    min-width:70px;
    text-align:center;
}

/* STATUS */

.status{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 14px;
    border-radius:10px;
    font-weight:600;
    font-size:13px;
}

.dot{
    width:8px;
    height:8px;
    border-radius:50%;
}

/* STATUS COLORS */

.served{
    background:#e8f8ee;
    color:#16a34a;
}

.served .dot{
    background:#16a34a;
}

.recalled{
    background:#fff5e6;
    color:#d97706;
}

.recalled .dot{
    background:#d97706;
}

.manual{
    background:#eaf2ff;
    color:#2563eb;
}

.manual .dot{
    background:#2563eb;
}

/* TIME */

.time{
    color:#475569;
    font-weight:500;
    font-size:14px;
}

/* EMPTY */

.empty{
    text-align:center;
    padding:30px;
    color:#94a3b8;
}

/* MOBILE */

@media(max-width:768px){

    body{
        padding:15px;
    }

    .page-header{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
    }

    .top-actions{
        width:100%;
        flex-wrap:wrap;
    }

    .table-wrapper{
        overflow-x:auto;
    }

    .custom-table{
        min-width:650px;
    }
}

</style>

</head>

<body>

<div class="page-header">

    <div class="page-title">
        <h1>Patient Logs</h1>
        <p>View recent queue activities and patient status updates.</p>
    </div>

    <div class="top-actions">
        <!-- BACK BUTTON -->
        <a href="../dashboard/dashboard.php" class="custom-btn">
            ← Back
        </a>
    </div>

</div>

<div class="table-wrapper">

<table class="table custom-table">

    <thead>
        <tr>
            <th>Queue Number</th>
            <th>Action</th>
            <th>Time</th>
        </tr>
    </thead>

    <tbody>

    <?php if($logs->num_rows > 0){ ?>

        <?php while($row = $logs->fetch_assoc()){ ?>

        <?php
        
        $action = strtolower($row['action']);

        $statusClass = "manual";

        if($action == "served"){
            $statusClass = "served";
        }
        elseif($action == "recalled"){
            $statusClass = "recalled";
        }

        ?>

        <tr>

            <td>
                <span class="queue-tag">
                    <?= $row['queue_number'] ?: '-' ?>
                </span>
            </td>

            <td>
                <span class="status <?= $statusClass ?>">
                    <span class="dot"></span>

                    <?= ucfirst(str_replace("_", " ", $row['action'])) ?>
                </span>
            </td>

            <td class="time">
                🕒 <?= $row['created_at'] ?>
            </td>

        </tr>

        <?php } ?>

    <?php } else { ?>

        <tr>
            <td colspan="3" class="empty">
                No patient logs available.
            </td>
        </tr>

    <?php } ?>

    </tbody>

</table>

</div>

</body>
</html>