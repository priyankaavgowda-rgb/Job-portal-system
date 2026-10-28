<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seeker') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';

$seeker_id = $_SESSION['user_id'];

// Get counts
$apps_count = $conn->query("SELECT COUNT(*) as count FROM applications WHERE seeker_id = $seeker_id")->fetch_assoc()['count'];
$shortlisted_count = $conn->query("SELECT COUNT(*) as count FROM applications WHERE seeker_id = $seeker_id AND status IN ('shortlisted', 'selected')")->fetch_assoc()['count'];

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action active"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="profile.php" class="list-group-item list-group-item-action"><i class="fas fa-user-circle me-2"></i>My Profile</a>
            <a href="applications.php" class="list-group-item list-group-item-action"><i class="fas fa-file-alt me-2"></i>My Applications</a>
            <a href="../jobs.php" class="list-group-item list-group-item-action"><i class="fas fa-search me-2"></i>Search Jobs</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <h3 class="mb-4">Job Seeker Dashboard</h3>
        <p>Welcome back, <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>!</p>
        
        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body text-center">
                        <h1 class="display-4"><i class="fas fa-paper-plane"></i></h1>
                        <h4><?= $apps_count ?></h4>
                        <p>Total Applications Sent</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body text-center">
                        <h1 class="display-4"><i class="fas fa-star"></i></h1>
                        <h4><?= $shortlisted_count ?></h4>
                        <p>Shortlisted / Selected</p>
                    </div>
                </div>
            </div>
        </div>
        
        <h4 class="mb-3">Recent Applications</h4>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Company</th>
                                <th>Date Applied</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_apps = $conn->query("SELECT a.*, j.title, COALESCE(j.company_name_override, e.company_name) as company_name 
                                                        FROM applications a 
                                                        JOIN jobs j ON a.job_id = j.id 
                                                        JOIN employer_profiles e ON j.employer_id = e.user_id 
                                                        WHERE a.seeker_id = $seeker_id 
                                                        ORDER BY a.applied_at DESC LIMIT 5");
                            if ($recent_apps->num_rows > 0):
                                while($app = $recent_apps->fetch_assoc()):
                            ?>
                            <tr>
                                <td><a href="../job_details.php?id=<?= $app['job_id'] ?>" class="text-decoration-none fw-bold"><?= htmlspecialchars($app['title']) ?></a></td>
                                <td><?= htmlspecialchars($app['company_name']) ?></td>
                                <td><?= date('M d, Y', strtotime($app['applied_at'])) ?></td>
                                <td>
                                    <?php
                                        $badge_class = 'bg-secondary';
                                        if ($app['status'] == 'shortlisted') $badge_class = 'bg-info';
                                        elseif ($app['status'] == 'selected') $badge_class = 'bg-success';
                                        elseif ($app['status'] == 'rejected') $badge_class = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= ucfirst($app['status']) ?></span>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" class="text-center">You haven't applied for any jobs yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
