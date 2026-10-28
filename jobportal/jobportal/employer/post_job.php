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
    $title = $conn->real_escape_string($_POST['title']);
    $category = $conn->real_escape_string($_POST['category']);
    $job_type = $conn->real_escape_string($_POST['job_type']);
    $location = $conn->real_escape_string($_POST['location']);
    $salary = $conn->real_escape_string($_POST['salary']);
    $deadline = $conn->real_escape_string($_POST['deadline']);
    $description = $conn->real_escape_string($_POST['description']);
    $requirements = $conn->real_escape_string($_POST['requirements']);

    $sql = "INSERT INTO jobs (employer_id, title, category, job_type, location, salary, deadline, description, requirements) 
            VALUES ($employer_id, '$title', '$category', '$job_type', '$location', '$salary', '$deadline', '$description', '$requirements')";
            
    if ($conn->query($sql) === TRUE) {
        $success = "Job posted successfully. It is now live on the portal.";
    }
}

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="profile.php" class="list-group-item list-group-item-action"><i class="fas fa-building me-2"></i>Company Profile</a>
            <a href="post_job.php" class="list-group-item list-group-item-action active"><i class="fas fa-plus-circle me-2"></i>Post a Job</a>
            <a href="jobs.php" class="list-group-item list-group-item-action"><i class="fas fa-briefcase me-2"></i>Manage Jobs</a>
            <a href="applications.php" class="list-group-item list-group-item-action"><i class="fas fa-users me-2"></i>Applications</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">Post a New Job</h4>
            </div>
            <div class="card-body">
                <?php if($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="title" class="form-label">Job Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="IT">IT & Software</option>
                                <option value="Finance">Finance</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Healthcare">Healthcare</option>
                                <option value="Sales and Business Development">Sales and Business Development</option>
                                <option value="HR and Administration">HR and Administration</option>
                                <option value="Teaching">Teaching</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="job_type" class="form-label">Job Type</label>
                            <select class="form-select" id="job_type" name="job_type" required>
                                <option value="Full Time">Full Time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Work From Home">Work From Home</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="salary" class="form-label">Salary (e.g. $50k - $70k)</label>
                            <input type="text" class="form-control" id="salary" name="salary" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="deadline" class="form-label">Application Deadline</label>
                            <input type="date" class="form-control" id="deadline" name="deadline" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Job Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="requirements" class="form-label">Requirements / Qualifications</label>
                        <textarea class="form-control" id="requirements" name="requirements" rows="4" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Post Job</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
