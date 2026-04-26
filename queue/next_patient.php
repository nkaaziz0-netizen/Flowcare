<?php
include("../config/config.php");

session_start();

if($_SESSION['role'] != "doctor"){
    header("Location: ../dashboard/dashboard.php");
    exit();
}

// STEP 1: check if there is waiting patient FIRST
$next = $conn->query("
    SELECT id FROM patients 
    WHERE status='waiting' 
    ORDER BY created_at ASC 
    LIMIT 1
");

if($next->num_rows > 0){

    // ✅ ONLY if there is next → proceed

    // get current serving
    $current = $conn->query("
        SELECT id FROM patients 
        WHERE status='serving' 
        LIMIT 1
    ");

    if($current->num_rows > 0){
        $row = $current->fetch_assoc();
        $current_id = $row['id'];

        // move to done
        $conn->query("
            UPDATE patients 
            SET status='done' 
            WHERE id = $current_id
        ");
    }

    // move next to serving
    $row = $next->fetch_assoc();
    $next_id = $row['id'];

    $conn->query("
        UPDATE patients 
        SET status='serving' 
        WHERE id = $next_id
    ");

}
// ❗ if NO waiting → do NOTHING (keep current serving)

header("Location: ../dashboard/dashboard.php");
exit();
?>