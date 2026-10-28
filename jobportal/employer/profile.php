<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';

$employer_id = $_SESSION['user_id'];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $company_description = $conn->real_escape_string($_POST['company_description']);
    $website = $conn->real_escape_string($_POST['website']);
    $location = $conn->real_escape_string($_POST['location']);

    $sql = "UPDATE employer_profiles SET 
            company_name = '$company_name', 
            company_description = '$company_description', 
            website = '$website', 
            location = '$location' 
            WHERE user_id = $employer_id";
            
    if ($conn->query($sql) === TRUE) {
        $success = "Profile updated successfully.";
    }
}

$profile = $conn->query("SELECT * FROM employer_profiles WHERE user_id = $employer_id")->fetch_assoc();

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="profile.php" class="list-group-item list-group-item-action active"><i class="fas fa-building me-2"></i>Company Profile</a>
            <a href="post_job.php" class="list-group-item list-group-item-action"><i class="fas fa-plus-circle me-2"></i>Post a Job</a>
            <a href="jobs.php" class="list-group-item list-group-item-action"><i class="fas fa-briefcase me-2"></i>Manage Jobs</a>
            <a href="applications.php" class="list-group-item list-group-item-action"><i class="fas fa-users me-2"></i>Applications</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">Company Profile</h4>
            </div>
            <div class="card-body">
                <?php if($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name" value="<?= htmlspecialchars($profile['company_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($profile['location'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="website" class="form-label">Website URL</label>
                        <input type="url" class="form-control" id="website" name="website" value="<?= htmlspecialchars($profile['website'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="company_description" class="form-label">Company Description</label>
                        <textarea class="form-control" id="company_description" name="company_description" rows="5" required><?= htmlspecialchars($profile['company_description'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
