<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seeker') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['job_id'])) {
    require_once '../includes/db.php';
    
    $job_id = (int)$_POST['job_id'];
    $seeker_id = $_SESSION['user_id'];
    
    // Check if job exists and is open
    $job_check = $conn->query("SELECT id FROM jobs WHERE id = $job_id AND status = 'open'");
    if ($job_check->num_rows > 0) {
        // Check if already applied
        $app_check = $conn->query("SELECT id FROM applications WHERE job_id = $job_id AND seeker_id = $seeker_id");
        if ($app_check->num_rows == 0) {
            $sql = "INSERT INTO applications (job_id, seeker_id) VALUES ($job_id, $seeker_id)";
            if ($conn->query($sql) === TRUE) {
                $_SESSION['message'] = "Application submitted successfully!";
                header("Location: applications.php");
                exit();
            }
        }
    }
    
    header("Location: ../job_details.php?id=$job_id");
    exit();
} else {
    header("Location: ../jobs.php");
    exit();
}
?>
