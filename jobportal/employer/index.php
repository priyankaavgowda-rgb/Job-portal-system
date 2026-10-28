<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';

$employer_id = $_SESSION['user_id'];

$user_email = $conn->query("SELECT email FROM users WHERE id = $employer_id")->fetch_assoc()['email'];
$is_master = ($user_email === 'employer@company.com');

// Get counts
if ($is_master) {
    $jobs_count = $conn->query("SELECT COUNT(*) as count FROM jobs")->fetch_assoc()['count'];
    $active_jobs = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE status = 'open'")->fetch_assoc()['count'];
    $apps_count = $conn->query("SELECT COUNT(id) as count FROM applications")->fetch_assoc()['count'];
} else {
    $jobs_count = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE employer_id = $employer_id")->fetch_assoc()['count'];
    $active_jobs = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE employer_id = $employer_id AND status = 'open'")->fetch_assoc()['count'];
    $apps_count = $conn->query("SELECT COUNT(a.id) as count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.employer_id = $employer_id")->fetch_assoc()['count'];
}

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action active"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="profile.php" class="list-group-item list-group-item-action"><i class="fas fa-building me-2"></i>Company Profile</a>
            <a href="post_job.php" class="list-group-item list-group-item-action"><i class="fas fa-plus-circle me-2"></i>Post a Job</a>
            <a href="jobs.php" class="list-group-item list-group-item-action"><i class="fas fa-briefcase me-2"></i>Manage Jobs</a>
            <a href="applications.php" class="list-group-item list-group-item-action"><i class="fas fa-users me-2"></i>Applications</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <h3 class="mb-4">Employer Dashboard</h3>
        <p>Welcome back, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>!</p>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h1 class="display-4"><i class="fas fa-briefcase"></i></h1>
                        <h4><?= $jobs_count ?></h4>
                        <p><?= $is_master ? 'Total Platform Jobs' : 'Total Jobs Posted' ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <h1 class="display-4"><i class="fas fa-check-circle"></i></h1>
                        <h4><?= $active_jobs ?></h4>
                        <p>Active Jobs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body text-center">
                        <h1 class="display-4"><i class="fas fa-users"></i></h1>
                        <h4><?= $apps_count ?></h4>
                        <p>Total Applications</p>
                    </div>
                </div>
            </div>
        </div>
        
        <h4 class="mt-4 mb-3">Recent Applications</h4>
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Job Title</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($is_master) {
                            $recent_apps = $conn->query("SELECT a.*, u.name, j.title, COALESCE(j.company_name_override, e.company_name) as display_company FROM applications a 
                                                        JOIN users u ON a.seeker_id = u.id 
                                                        JOIN jobs j ON a.job_id = j.id 
                                                        JOIN employer_profiles e ON j.employer_id = e.user_id 
                                                        ORDER BY a.applied_at DESC LIMIT 5");
                        } else {
                            $recent_apps = $conn->query("SELECT a.*, u.name, j.title FROM applications a 
                                                        JOIN users u ON a.seeker_id = u.id 
                                                        JOIN jobs j ON a.job_id = j.id 
                                                        WHERE j.employer_id = $employer_id 
                                                        ORDER BY a.applied_at DESC LIMIT 5");
                        }
                        if ($recent_apps->num_rows > 0):
                            while($app = $recent_apps->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($app['name']) ?></td>
                            <td><?= htmlspecialchars($app['title']) ?></td>
                            <td><span class="badge bg-secondary"><?= ucfirst($app['status']) ?></span></td>
                            <td><?= date('M d, Y', strtotime($app['applied_at'])) ?></td>
                            <td><a href="applications.php" class="btn btn-sm btn-primary">View</a></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="5" class="text-center">No recent applications.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
