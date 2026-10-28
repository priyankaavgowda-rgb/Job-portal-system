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

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['app_id']) && isset($_POST['status'])) {
    $app_id = (int)$_POST['app_id'];
    $status = $conn->real_escape_string($_POST['status']);
    
    // Verify the application belongs to a job posted by this employer
    if ($is_master) {
        $verify = $conn->query("SELECT id FROM applications WHERE id = $app_id");
    } else {
        $verify = $conn->query("SELECT a.id FROM applications a JOIN jobs j ON a.job_id = j.id WHERE a.id = $app_id AND j.employer_id = $employer_id");
    }
    
    if ($verify->num_rows > 0) {
        $conn->query("UPDATE applications SET status = '$status' WHERE id = $app_id");
    }
}

// Fetch applications
if ($is_master) {
    $sql = "SELECT a.*, u.name as seeker_name, u.email as seeker_email, s.phone, s.skills, s.education, s.resume_path, j.title as job_title, COALESCE(j.company_name_override, e.company_name) as display_company 
            FROM applications a 
            JOIN users u ON a.seeker_id = u.id 
            JOIN seeker_profiles s ON u.id = s.user_id 
            JOIN jobs j ON a.job_id = j.id 
            JOIN employer_profiles e ON j.employer_id = e.user_id 
            ORDER BY a.applied_at DESC";
} else {
    $sql = "SELECT a.*, u.name as seeker_name, u.email as seeker_email, s.phone, s.skills, s.education, s.resume_path, j.title as job_title 
            FROM applications a 
            JOIN users u ON a.seeker_id = u.id 
            JOIN seeker_profiles s ON u.id = s.user_id 
            JOIN jobs j ON a.job_id = j.id 
            WHERE j.employer_id = $employer_id 
            ORDER BY a.applied_at DESC";
}
$applications = $conn->query($sql);

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            <a href="profile.php" class="list-group-item list-group-item-action"><i class="fas fa-building me-2"></i>Company Profile</a>
            <a href="post_job.php" class="list-group-item list-group-item-action"><i class="fas fa-plus-circle me-2"></i>Post a Job</a>
            <a href="jobs.php" class="list-group-item list-group-item-action"><i class="fas fa-briefcase me-2"></i>Manage Jobs</a>
            <a href="applications.php" class="list-group-item list-group-item-action active"><i class="fas fa-users me-2"></i>Applications</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">Job Applications</h4>
            </div>
            <div class="card-body">
                <?php if ($applications->num_rows > 0): ?>
                    <div class="accordion" id="appsAccordion">
                        <?php while($app = $applications->fetch_assoc()): ?>
                            <div class="accordion-item mb-2 border rounded">
                                <h2 class="accordion-header" id="heading<?= $app['id'] ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $app['id'] ?>" aria-expanded="false" aria-controls="collapse<?= $app['id'] ?>">
                                        <div class="d-flex justify-content-between w-100 me-3">
                                            <div>
                                                <strong><?= htmlspecialchars($app['seeker_name']) ?></strong> applied for <strong><?= htmlspecialchars($app['job_title']) ?></strong>
                                                <?php if($is_master): ?><br><small class="text-muted"><i class="fas fa-building me-1"></i><?= htmlspecialchars($app['display_company']) ?></small><?php endif; ?>
                                            </div>
                                            <div>
                                                <?php
                                                    $badge_class = 'bg-secondary';
                                                    if ($app['status'] == 'shortlisted') $badge_class = 'bg-info';
                                                    elseif ($app['status'] == 'selected') $badge_class = 'bg-success';
                                                    elseif ($app['status'] == 'rejected') $badge_class = 'bg-danger';
                                                ?>
                                                <span class="badge <?= $badge_class ?>"><?= ucfirst($app['status']) ?></span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse<?= $app['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $app['id'] ?>" data-bs-parent="#appsAccordion">
                                    <div class="accordion-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <p><strong><i class="fas fa-envelope me-2 text-muted"></i>Email:</strong> <a href="mailto:<?= htmlspecialchars($app['seeker_email']) ?>"><?= htmlspecialchars($app['seeker_email']) ?></a></p>
                                                <p><strong><i class="fas fa-phone me-2 text-muted"></i>Phone:</strong> <?= htmlspecialchars($app['phone'] ?? 'N/A') ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong><i class="fas fa-calendar-alt me-2 text-muted"></i>Applied On:</strong> <?= date('M d, Y H:i', strtotime($app['applied_at'])) ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6><strong>Skills:</strong></h6>
                                            <p><?= nl2br(htmlspecialchars($app['skills'] ?? 'Not provided')) ?></p>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <h6><strong>Education:</strong></h6>
                                            <p><?= nl2br(htmlspecialchars($app['education'] ?? 'Not provided')) ?></p>
                                        </div>
                                        
                                        <?php if (!empty($app['resume_path'])): ?>
                                        <div class="mb-4">
                                            <h6><strong>Resume:</strong></h6>
                                            <a href="<?= htmlspecialchars($app['resume_path']) ?>" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fas fa-file-download me-2"></i>Download / View Resume</a>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <form method="POST" action="" class="d-flex align-items-center bg-light p-3 rounded">
                                            <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                                            <label class="me-3 fw-bold">Update Status:</label>
                                            <select name="status" class="form-select w-auto me-3">
                                                <option value="applied" <?= $app['status'] == 'applied' ? 'selected' : '' ?>>Applied</option>
                                                <option value="shortlisted" <?= $app['status'] == 'shortlisted' ? 'selected' : '' ?>>Shortlisted</option>
                                                <option value="selected" <?= $app['status'] == 'selected' ? 'selected' : '' ?>>Selected</option>
                                                <option value="rejected" <?= $app['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-primary btn-sm">Save</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center py-4 text-muted">No applications received yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
