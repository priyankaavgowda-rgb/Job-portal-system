<?php
session_start();
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: jobs.php");
    exit();
}

$job_id = (int)$_GET['id'];
$sql = "SELECT j.*, COALESCE(j.company_name_override, e.company_name) as company_name, e.company_description, e.website 
        FROM jobs j 
        JOIN employer_profiles e ON j.employer_id = e.user_id 
        WHERE j.id = $job_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: jobs.php");
    exit();
}

$job = $result->fetch_assoc();

// Check if already applied
$has_applied = false;
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'seeker') {
    $seeker_id = $_SESSION['user_id'];
    $check_app = "SELECT id FROM applications WHERE job_id = $job_id AND seeker_id = $seeker_id";
    $app_result = $conn->query($check_app);
    if ($app_result->num_rows > 0) {
        $has_applied = true;
    }
}

include 'includes/header.php';
?>

<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body p-5">
                <h2 class="fw-bold text-primary mb-3"><?= nl2br(htmlspecialchars($job['title'])) ?></h2>
                <div class="d-flex flex-wrap text-secondary mb-4 gap-3">
                    <span><i class="fas fa-building me-2"></i><?= htmlspecialchars($job['company_name']) ?></span>
                    <span><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($job['location']) ?></span>
                    <span><i class="fas fa-money-bill-wave me-2"></i><?= htmlspecialchars($job['salary']) ?></span>
                    <span><i class="fas fa-briefcase me-2"></i><?= htmlspecialchars($job['category']) ?></span>
                    <span><i class="fas fa-clock me-2"></i><?= htmlspecialchars($job['job_type'] ?? 'Full Time') ?></span>
                </div>
                
                <h4 class="fw-bold mt-4 mb-3 border-bottom pb-2">Job Description</h4>
                <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                
                <h4 class="fw-bold mt-4 mb-3 border-bottom pb-2">Requirements</h4>
                <p><?= nl2br(htmlspecialchars($job['requirements'])) ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 mb-4 bg-light">
            <div class="card-body text-center p-4">
                <h5 class="fw-bold mb-3">Job Summary</h5>
                <ul class="list-group list-group-flush text-start mb-4">
                    <li class="list-group-item bg-transparent"><strong>Posted:</strong> <?= date('M d, Y', strtotime($job['created_at'])) ?></li>
                    <li class="list-group-item bg-transparent"><strong>Deadline:</strong> <?= date('M d, Y', strtotime($job['deadline'])) ?></li>
                    <li class="list-group-item bg-transparent"><strong>Status:</strong> <span class="badge <?= $job['status'] == 'open' ? 'bg-success' : 'bg-danger' ?>"><?= ucfirst($job['status']) ?></span></li>
                </ul>

                <?php if ($job['status'] == 'open'): ?>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="alert alert-info">Please <a href="login.php" class="fw-bold">login</a> as a job seeker to apply.</div>
                    <?php elseif ($_SESSION['role'] == 'seeker'): ?>
                        <?php if ($has_applied): ?>
                            <button class="btn btn-success btn-lg w-100" disabled><i class="fas fa-check-circle me-2"></i>Already Applied</button>
                        <?php else: ?>
                            <form action="seeker/apply.php" method="POST">
                                <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">Apply Now</button>
                            </form>
                        <?php endif; ?>
                    <?php elseif ($_SESSION['role'] == 'employer'): ?>
                        <div class="alert alert-warning">Employers cannot apply for jobs.</div>
                    <?php endif; ?>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg w-100" disabled>Job Closed</button>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="fw-bold border-bottom pb-2 mb-3">About Company</h5>
                <h6 class="fw-bold text-primary"><?= htmlspecialchars($job['company_name']) ?></h6>
                <p class="small text-muted"><?= nl2br(htmlspecialchars($job['company_description'])) ?></p>
                <?php if (!empty($job['website'])): ?>
                    <a href="<?= htmlspecialchars($job['website']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary w-100"><i class="fas fa-globe me-2"></i>Visit Website</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
