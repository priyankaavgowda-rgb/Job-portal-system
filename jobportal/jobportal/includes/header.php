<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$base_url = "http://localhost/jobportal/";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= $base_url ?>index.php"><i class="fas fa-briefcase me-2"></i>Job Portal</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?= $base_url ?>index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= $base_url ?>jobs.php">Find Jobs</a>
        </li>
        <?php if(isset($_SESSION['user_id'])): ?>
            <?php if($_SESSION['role'] == 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>admin/index.php">Dashboard</a></li>
            <?php elseif($_SESSION['role'] == 'employer'): ?>
                <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>employer/index.php">Dashboard</a></li>
            <?php elseif($_SESSION['role'] == 'seeker'): ?>
                <li class="nav-item"><a class="nav-link" href="<?= $base_url ?>seeker/index.php">Dashboard</a></li>
            <?php endif; ?>
            <li class="nav-item">
              <a class="nav-link text-danger" href="<?= $base_url ?>logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        <?php else: ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= $base_url ?>login.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link btn btn-light text-primary ms-2 px-3 fw-bold" href="<?= $base_url ?>register.php">Register</a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container my-4 min-vh-100">
